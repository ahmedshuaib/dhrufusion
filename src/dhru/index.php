<?php

session_name("DHRUFUSION");
session_set_cookie_params(0, "/", null, false, true);
session_start();
error_reporting(0);

function post_request($url, $data)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json', // Ensure you set the correct content type for JSON
        'Accept: application/json'
    ]);
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($curl);
    if (curl_errno($curl) != CURLE_OK) {
        echo curl_error($curl);
        curl_close($curl);
    } else {
        curl_close($curl);
        return $response;
    }
}

$response = post_request("https://ultimatefrp.eu/api/dhru", json_encode($_POST));

header('Content-Type: application/json; charset=utf-8');
header_remove('pragma');
header_remove('server');
header_remove('transfer-encoding');
header_remove('cache-control');
header_remove('expires');

exit($response);
