<?php

namespace TFMSoftware\DhruFusion\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TFMSoftware\DhruFusion\Models\DhruFusion;
use App\Helper\Option\OptionHelperFacades as opt;
use App\Http\Controllers\Admin\Api\OrdersController;
use App\Http\Controllers\Admin\Api\LicenseController;
use App\Models\Order;

class DhruApiController extends Controller
{

    public $site_url = "http://test.ahmedshuaib.com";
    public $orders;
    public $license;

    public function __construct()
    {
        $this->license = new LicenseController;
        $this->orders = new OrdersController($this->license);
    }

    public function dhru_login(Request $request)
    {

        $request->validate([
            'username' => 'required',
            'key'    => 'required'
        ]);

        $key = DhruFusion::where('api_key', $request->key)->firstorfail();

        Auth::loginUsingId($key->user_id);

        if (auth()->user()->is_admin || auth()->user()->is_system) {
            $msg = [
                'success' => true,
            ];
        } else {
            $msg = [
                'success' => false
            ];
        }

        return response($msg, 200);
    }

    public function account_info(Request $request)
    {

        $this->dhru_login($request);


        return response()->json([
            'account' => [
                'name' => auth()->user()->username,
                'email' => auth()->user()->email,
                'currency' => opt::system()['currency_suffix'],
                'balance' => auth()->user()->balance . ' ' . opt::system()['currency_suffix']
            ]
        ]);
    }

    public function email_to_id(Request $request)
    {
        $request->validate([
            'email' => 'required'
        ]);
        $user = User::where('email', $request->email)->firstorfail();
        return response(['uid' => $user->id], 200);
    }

    public function license_order(Request $request)
    {
        $this->dhru_login($request);


        $this->authorize('order_create'); //permission check

        $resp = $this->orders->store($request);

        return $resp;
    }

    public function order_show(Request $request)
    {

        $this->dhru_login($request);

        $request->validate([
            'order_id' => 'required'
        ]);

        $order = Order::findorfail($request->order_id);

        //check auth id to order id
        if ($order->order_uid == auth()->id()) {
            return response()->json([
                'status' => true,
                'order_id' => $order->id,
                'code' => 4,
                'msg' => 'Order completed successfully!',
            ], 200);
        }

        return response()->json([
            'status' => true,
            'msg' => 'Order not found / Order admin is other person',
            'code' => 3,
        ]);
    }

    public function credit_order(Request $request)
    {

        $request->validate([
            'amount' => 'required',
            'email' => 'required'
        ]);
    }
}
