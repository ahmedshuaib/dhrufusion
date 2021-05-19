<?php

use Illuminate\Support\Facades\Route;
use TFMSoftware\DhruFusion\Http\Controllers;

Route::namespace(Controllers::class)->prefix('admin')
->middleware(['web', 'auth:admin', 'admin', 'verified'])->group(function () {
    //api key genrate and modify here
    Route::apiResource('/dhru/api/key', ApiKeyController::class);
});


Route::namespace(Controllers::class)->middleware(['api'])->prefix('system/api')->group(function () {
    Route::post('dhru/login',  'DhruApiController@dhru_login');
});

Route::namespace(Controllers::class)->prefix('system/api/dhru')
->middleware(['api'])->group(function () {
    Route::post('account', 'DhruApiController@account_info');
    Route::post('uid', 'DhruApiController@email_to_id');
    Route::post('order_license', 'DhruApiController@license_order');
    Route::post('order', 'DhruApiController@order_show');
});
