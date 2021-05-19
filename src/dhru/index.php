<?php

/**
 * DHRU Fusion api standards V6.1
 */

session_name("DHRUFUSION");
session_set_cookie_params(0, "/", null, false, true);
session_start();
error_reporting(0);
$apiversion = '6.1';
foreach ($_POST as $k => $v) {
    ${$k} = filter_var($v, FILTER_SANITIZE_STRING);
}

$site_url = "http://test.ahmedshuaib.com";

$apiresults = array();
if ($parameters) {
    $parameters = json_decode(base64_decode($parameters), true);
}


//extra work
$xml_array = simplexml_load_string($_POST['parameters']); //FIRST LOAD XML
$json_enc = json_encode($xml_array); //CONVERT TO JSON
$tfm_param = json_decode($json_enc, true); //DECODE JSON

if ($User = validateAuth($username, $apiaccesskey)) {

    $data = array(
        'username' => $username,
        'key'    => $apiaccesskey
    );

    switch ($action) {

        case "accountinfo":
            $resp = post_request($site_url . '/system/api/dhru/account', $data);
            $resp = json_decode($resp, true)['account'];
            $AccoutInfo['credit'] = $resp['balance'];
            $AccoutInfo['mail'] = $resp['email'];
            $AccoutInfo['currency'] = $resp['currency']; /* Currency code */
            $apiresults['SUCCESS'][] = array('message' => 'Your Accout Info', 'AccoutInfo' => $AccoutInfo);
            break;

        case "imeiservicelist":
            $resp = post_request($site_url . '/system/api/packages', $data);
            $resp = json_decode($resp, true)['packages'];

            $ServiceList = NULL;
            $Group = 'Service Group';
            $ServiceList[$Group]['GROUPNAME'] = $Group;
            $ServiceList[$Group]['GROUPTYPE'] = 'SERVER'; // IMEI OR SERVER OR REMOTE

            foreach ($resp as $key => $val) {
                $SERVICEID = $val['id'];
                $ServiceList[$Group]['GROUPTYPE'] = 'SERVER';  //IMEI OR SERVER
                $ServiceList[$Group]['SERVICES'][$SERVICEID]['SERVICEID'] = $SERVICEID;
                $ServiceList[$Group]['SERVICES'][$SERVICEID]['SERVICETYPE'] = 'SERVER'; // IMEI OR SERVER OR REMOTE
                $ServiceList[$Group]['SERVICES'][$SERVICEID]['SERVICENAME'] = $val['package_name'];
                $ServiceList[$Group]['SERVICES'][$SERVICEID]['CREDIT'] = $val['price'];
                $ServiceList[$Group]['SERVICES'][$SERVICEID]['INFO'] = utf8_encode($val['package_description']);
                $ServiceList[$Group]['SERVICES'][$SERVICEID]['TIME'] = 'Instant';

                /*QNT*/
                $ServiceList[$Group]['SERVICES'][$SERVICEID]['QNT'] = 1;
                $ServiceList[$Group]['SERVICES'][$SERVICEID]['QNTOPTIONS'] = '1';
                $ServiceList[$Group]['SERVICES'][$SERVICEID]['MINQNT'] = '1'; /* QNTOPTIONS OR MIN/MAX QNT*/
                $ServiceList[$Group]['SERVICES'][$SERVICEID]['MAXQNT'] = '1';

                /*Custom Fields*/
                $CUSTOM = array(); {
                    $CUSTOM[0]['type'] = 'serviceimei';
                    $CUSTOM[0]['fieldname'] = 'email';
                    $CUSTOM[0]['fieldtype'] = 'text'; /* text dropdown radio textarea tickbox datepicker time */
                    $CUSTOM[0]['description'] = '';
                    $CUSTOM[0]['fieldoptions'] = '';
                    $CUSTOM[0]['required'] = 1;
                }
                $ServiceList[$Group]['SERVICES'][$SERVICEID]['Requires.Custom'] = $CUSTOM;
            }

            $apiresults['SUCCESS'][] = array('MESSAGE' => 'IMEI Service List', 'LIST' => $ServiceList);
            break;

        case "placeimeiorder":

            $ServiceId = $tfm_param['ID']; //SERVICE ID
            $CustomField = json_decode(base64_decode($tfm_param['CUSTOMFIELD']), true); //CUSTOMFIELD

            $field = array(
                'email' => $CustomField['email']
            );

            $resp = post_request($site_url . '/system/api/dhru/uid', $field);

            $resp = json_decode($resp, true);

            $field = array(
                'user_id' => $resp['uid'],
                'package_id' => $ServiceId,
                'is_active' => 1,
            );

            $field = array_merge($field, $data);

            $resp = post_request($site_url . '/system/api/dhru/order_license', $field);

            $resp = json_decode($resp, true);

            if ($resp['status'] == true) {
                /*  Process order and ger order reference id*/
                $order_reff_id = $resp['refid'];
                $apiresults['SUCCESS'][] = array('MESSAGE' => $resp['msg'], 'REFERENCEID' => $order_reff_id);
            } else {
                $msg = empty($resp['message']) ? $resp['msg'] : $resp['message'];
                $apiresults['ERROR'][] = array('MESSAGE' => $msg);
            }
            break;

        case "getimeiorder":
            $OrderID = $tfm_param['ID']; //order id

            $field = array(
                'order_id' => $OrderID,
            );

            $field = array_merge($data, $field);

            $resp = post_request($site_url . '/system/api/dhru/order', $field);

            $resp = json_decode($resp, true);

            $code = 1;
            $code = $resp['status'] == true ? $resp['code'] : 3;
            $msg = $resp['status'] == true ? $resp['msg'] : 'Try again';

            $apiresults['SUCCESS'][] = array(
                'STATUS' => $code, /* 0 - New , 1 - InProcess, 3 - Reject(Refund), 4- Available(Success)  */
                'CODE' => $msg
            );

            break;
        default:
            $apiresults['ERROR'][] = array('MESSAGE' => 'Invalid Action');
    }
} else {
    $apiresults['ERROR'][] = array('MESSAGE' => 'Authentication Failed');
}



function validateAuth($username, $apiKey)
{
    $data = array(
        'username' => $username,
        'key'   => $apiKey
    );

    $resp = post_request('http://test.ahmedshuaib.com/system/api/dhru/login', $data);

    $result = json_decode($resp, true);
    if ($result['success']) return (true);
    else return (false);
}


function post_request($url, $data)
{
    $crul = curl_init();
    curl_setopt($crul, CURLOPT_HEADER, false);
    curl_setopt($crul, CURLOPT_HTTPHEADER, array(
        'Accept: application/json'
    ));
    curl_setopt($crul, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($crul, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($crul, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($crul, CURLOPT_URL, $url);
    curl_setopt($crul, CURLOPT_POST, true);
    curl_setopt($crul, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($crul, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($crul);
    if (curl_errno($crul) != CURLE_OK) {
        echo curl_error($crul);
        curl_close($crul);
    } else {
        curl_close($crul);
        return $response;
    }
}

if (count($apiresults)) {
    header("X-Powered-By: DHRU-FUSION");
    header("dhru-fusion-api-version: $apiversion");
    header_remove('pragma');
    header_remove('server');
    header_remove('transfer-encoding');
    header_remove('cache-control');
    header_remove('expires');
    header('Content-Type: application/json; charset=utf-8');
    $apiresults['apiversion'] = $apiversion;
    exit(json_encode($apiresults));
}
