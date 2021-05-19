<?php

use Illuminate\Support\Facades\Route;

use TFMSoftware\DhruFusion\Http\Controllers;
use TFMSoftware\DhruFusion\Http\Controllers\DhruApiController;

Route::namespace(Controllers::class)->prefix('admin')
->middleware(['web', 'auth:admin', 'admin', 'verified'])->group(function () {
    //api key genrate and modify here

    Route::prefix('dhru/api')->group(function() {

        Route::apiResource('key', ApiKeyController::class);

        Route::post('login',  'DhruApiController@dhru_login');
        Route::post('account', 'DhruApiController@account_info');
        Route::post('order_license', 'DhruApiController@license_order');

    });

});
