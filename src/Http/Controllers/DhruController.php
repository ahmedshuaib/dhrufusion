<?php

namespace TFMSoftware\DhruFusion\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Api\OrdersCollection;
use Illuminate\Support\Facades\Log;
use TFMSoftware\DhruFusion\Helpers\Responses\DhruResponse;
use TFMSoftware\DhruFusion\Services\AccountInfoService;
use TFMSoftware\DhruFusion\Services\CreditService;
use TFMSoftware\DhruFusion\Services\LicenseService;
use Illuminate\Support\Facades\Validator;
use TFMSoftware\DhruFusion\Services\PackageService;

class DhruController extends Controller
{

    protected $credit;
    protected $license;
    protected $info;
    protected $packages;
    protected $response;

    public function __construct(AccountInfoService $info, CreditService $credit, LicenseService $license, PackageService $packages, DhruResponse $response)
    {
        $this->info = $info;
        $this->credit = $credit;
        $this->license = $license;
        $this->packages = $packages;
        $this->response = $response;
    }


    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|string',
            'parameters' => 'nullable|string',
            'username' => 'required_with:action,accountinfo,imeiservicelist|string',
            'apiaccesskey' => 'required_with:action,accountinfo,imeiservicelist|string',
        ]);

        if($validator->fails()) {
            Log::info($validator->errors()->first());
            return $this->response->errorResponse($validator->errors()->first());
        }

        // Decode parameters
        $parameters = $this->decodeParameters($request);
        Log::info("Parameters: ", [$parameters]);

        // Action
        $action = $request->input('action');

        try {
            $apiResult = match($action) {
                'accountinfo' => $this->accountInfo(),
                'imeiservicelist' => $this->imeiservicelist(),
                'placeimeiorder' => $this->placeimeiorder($parameters),
                'getimeiorder' => $this->getimeiorder($parameters),
                default => $this->response->errorResponse('Invalid action'),
            };
            return $apiResult;
        }
        catch(\Exception $e) {
            Log::error($e->getMessage());
            return $this->response->errorResponse($e->getMessage());
        }
    }

    private function accountInfo() {
        return $this->response->successResponse($this->info->accountInfo());
    }

    private function imeiservicelist() {

        $services = $this->packages->getPackages();

        $ServiceList = NULL;
        $ServiceList = [];
        $Group = 'Service Group';
        $ServiceList[$Group] = [
            'GROUPNAME' => $Group,
            'GROUPTYPE' => 'SERVER',
            'SERVICES' => []
        ];

        foreach ($services as $service) {
            $serviceId = $service->id;

            $customFields = [
                [
                    'type' => 'serviceimei',
                    'fieldname' => 'email',
                    'fieldtype' => 'text',
                    'description' => 'Enter your email',
                    'fieldoptions' => '',
                    'required' => 'on',
                ]
            ];

            $ServiceList[$Group]['SERVICES'][$serviceId] = [
                'SERVICEID' => $serviceId,
                'SERVICETYPE' => 'SERVER',
                'SERVICENAME' => $service->package_name,
                'CREDIT' => $service->price,
                'INFO' => utf8_encode($service->package_description),
                'TIME' => 'Instant',
                'Requires.Custom' => $customFields
            ];
        }

        // //custom package for credit service
        {

            $customFields = [
                [
                    'type' => 'serviceimei',
                    'fieldname' => 'email',
                    'fieldtype' => 'text',
                    'description' => 'Enter your email',
                    // 'fieldoptions' => '',
                    'required' => 'on',
                ]
            ];

            $serviceId = 7878;

            $serviceName = config('app.name');

            $ServiceList[$Group]['SERVICES'][$serviceId] = [
                'SERVICEID' => $serviceId,
                'SERVICETYPE' => 'SERVER',
                'SERVICENAME' => $serviceName . ' Credit Recharge Service',
                'CREDIT' => 1.0,
                'INFO' => utf8_encode("Buy $serviceName credit instant service"),
                'TIME' => 'Instant',
                'QNT' => 1,
                'QNTOPTIONS' => '',
                'MINQNT' => '5', /* QNTOPTIONS OR MIN/MAX QNT*/
                'MAXQNT' => '',
                'Requires.Custom' => $customFields
            ];

        }

        return $this->response->successResponse(['MESSAGE' => 'IMEI Service List', 'LIST' => $ServiceList]);
    }

    private function placeimeiorder($parameters) {
        if($parameters->ID == 7878) {
            $this->authorize('transfer_control');
            return $this->placeCreditOrder($parameters);
        }
        $this->authorize('order_create'); //permission check
        return $this->placeLicenseOrder($parameters);
    }

    private function placeCreditOrder($parameters) {

        $customParameter = json_decode(base64_decode($parameters->CUSTOMFIELD));

        Log::info("Custom Parameter: ", [$customParameter]);

        $form_data = [
            'email' => $customParameter->email,
            'amount' => $parameters->QNT,
        ];

        $handler = $this->credit->creditTransfer(new Request($form_data));
        $response = $handler->original;
        if($handler->isOk() && $response['status'] == true) {
            return $this->response->successResponse(['MESSAGE' => 'Credit added successfully', 'REFERENCEID' => $response['refid'] . ' - CREDIT']);
        }
        return $this->response->errorResponse(empty($response['message']) ? 'Credit not added' : $response['message']);
    }

    private function placeLicenseOrder($parameters) {

        $serviceId = $parameters->ID;
        $customParameter = json_decode(base64_decode($parameters->CUSTOMFIELD));

        Log::info("Custom Parameter: ", [$customParameter]);
        $form_data = [
            'email' => $customParameter->email,
            'package_id' => $serviceId,
        ];

        $handler = $this->license->orderLicense(new Request($form_data));
        $response = $handler->original;
        if($handler->isOk() && $response['status'] == true) {
            return $this->response->successResponse(['MESSAGE' => 'Order placed successfully', 'REFERENCEID' => $response['refid']]);
        }
        return $this->response->errorResponse(empty($response['message']) ? $response['msg'] : $response['message']);
    }

    private function getimeiorder($parameters) {
        if(strpos($parameters->ID, 'CREDIT') !== false) {
            return $this->getCreditOrder($parameters);
        }
        return $this->getLicenseOrder($parameters);
    }

    private function getCreditOrder($parameters) {
        preg_match_all('!\d+!', $parameters->ID, $OrderID);
        $info = $this->credit->getCreditOrder($OrderID[0][0]);
        Log::info("Credit Order Info: ", [$info]);
        if(empty($info)) {
            return $this->response->successResponse(['STATUS' => 3, 'CODE' => 'Credit order is rejected']);
        }
        return $this->response->successResponse(['STATUS' => 4, 'CODE' =>  round($info->amount) . ' Credit successfullly added!']);
    }

    private function getLicenseOrder($parameters) {
        $info = $this->license->showLicense($parameters->ID);
        Log::info("License Order Info: ", [$info]);
        if(empty($info)) {
            return $this->response->successResponse(['STATUS' => 3, 'CODE' => 'License order is rejected!']);
        }
        return $this->response->successResponse(['STATUS' => 4, 'CODE' => $info->package->package_name . ' activated successfully!']);
    }

    private function decodeParameters($request)
    {
        $parameters = $request->input('parameters');
        if ($parameters) {
            $parameters = json_decode(base64_decode($parameters));
        }

        // If 'parameters' is expected to be in XML format in a POST request
        if ($request->has('parameters')) {
            $xmlString = $request->input('parameters');
            $xmlObject = simplexml_load_string($xmlString);
            $jsonString = json_encode($xmlObject);
            $parameters = json_decode($jsonString);
        }

        return $parameters;
    }
}
