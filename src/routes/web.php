<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

use TFMSoftware\DhruFusion\Http\Controllers;
use TFMSoftware\DhruFusion\Http\Controllers\DhruApiController;

Route::namespace(Controllers::class)->prefix('admin')
->middleware(['web', 'auth:admin', 'admin', 'verified'])->group(function () {
    //api key genrate and modify here
    Route::apiResource('/dhru/api/key', ApiKeyController::class);

});


Route::post('api/dhru/login',  'DhruApiController@dhru_login')->middleware(['api', 'guest:admin']);

Route::namespace(Controllers::class)->prefix('/dhru')
->middleware(['api', 'auth:admin', 'admin', 'verified'])->group(function() {
    Route::post('account', 'DhruApiController@account_info');
    Route::post('order_license', 'DhruApiController@license_order');
});

