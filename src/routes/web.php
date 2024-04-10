<?php

use Illuminate\Support\Facades\Route;
use TFMSoftware\DhruFusion\Http\Controllers;
use TFMSoftware\DhruFusion\Http\Controllers\DhruController;

Route::namespace(Controllers::class)->prefix('admin')
->middleware(['web', 'auth:admin', 'admin', 'verified'])->group(function () {
    //api key genrate and modify here
    Route::apiResource('/dhru/api/key', ApiKeyController::class);
});

Route::namespace(Controllers::class)->prefix('api')
->middleware(['api', 'dhru.auth'])->post('/dhru', DhruController::class)
->name('dhru.api');
