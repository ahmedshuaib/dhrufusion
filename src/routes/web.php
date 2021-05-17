<?php

use Illuminate\Support\Facades\Route;

Route::namespace(TFMSoftware\DhruFusion\Http\Controllers::class)->prefix('admin')->group(function() {
    //api key genrate and modify here
    Route::apiResource('/dhru/api/key', ApiKeyController::class);
});

