<?php

namespace TFMSoftware\DhruFusion\Services;

use App\Http\Controllers\Admin\Api\TransfersController;

class CreditService {

    protected $credit;

    public function __construct(TransfersController $credit)
    {
        $this->credit = $credit;
    }

    public function creditTransfer($request)
    {
        $resp = $this->credit->store($request);
        return $resp;
    }

    public function getCreditOrder($request)
    {
        $resp = $this->credit->show($request);
        return $resp->auth_id == auth()->user()->id ? $resp : null;
    }

}
