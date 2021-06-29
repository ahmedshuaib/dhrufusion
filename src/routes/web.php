<?php

use Illuminate\Support\Facades\Route;
use TFMSoftware\DhruFusion\Http\Controllers;

Route::namespace(Controllers::class)->prefix('admin')
->middleware(['web', 'auth:admin', 'admin', 'verified'])->group(function () {
    //api key genrate and modify here
    Route::apiResource('/dhru/api/key', ApiKeyController::class);
});


Route::namespace(Controllers::class)->middleware(['api'])->prefix('system/api')->group(function () {
    Route::post('dhru/login',  'DhruApiController@dhru_login')->name('dhru.login');
});

Route::namespace(Controllers::class)->prefix('system/api/dhru')
->middleware(['api'])->group(function () {
    Route::post('account', 'DhruApiController@account_info')->name('dhru.account.info');
    Route::post('uid', 'DhruApiController@email_to_id')->name('dhru.email-to.id');
    Route::post('order-license', 'DhruApiController@license_order')->name('dhru.order-license');
    Route::post('order-credit', 'DhruApiController@credit_order')->name('dhru.order.credit');

    Route::post('get-order', 'DhruApiController@order_show')->name('dhru.order.show');
    Route::post('get-credit-order', 'DhruApiController@get_credit_order')->name('dhru.credit.show');
});
