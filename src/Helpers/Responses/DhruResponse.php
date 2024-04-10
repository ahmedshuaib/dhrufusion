<?php

namespace TFMSoftware\DhruFusion\Helpers\Responses;

class DhruResponse {

    public function successResponse($data)
    {
        return response(['SUCCESS' => [$data], 'apiversion' => 6.1])
        ->header("X-Powered-By", "DHRU-FUSION")
        ->header("dhru-fusion-api-version", "6.1")
        ->header('Content-Type', 'application/json; charset=utf-8');
    }

    public function errorResponse($message)
    {
        return response(['ERROR' => [['MESSAGE' => $message]], 'apiversion' => 6.1])
        ->header("X-Powered-By", "DHRU-FUSION")
        ->header("dhru-fusion-api-version", "6.1")
        ->header('Content-Type', 'application/json; charset=utf-8');
    }
}
