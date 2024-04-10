<?php

namespace TFMSoftware\DhruFusion\Services;

use App\Http\Controllers\Admin\Api\LicenseController;
use App\Http\Controllers\Admin\Api\OrdersController;
use App\User;

class LicenseService {

    protected $order;

    public function __construct(LicenseController $license)
    {
        $this->order = new OrdersController($license);
    }

    public function orderLicense($request)
    {
        $user = User::where('email', $request->email)->firstorfail();

        $request->merge([
            'user_id' => $user->id,
            'is_active' => 1,
        ]);

        $resp = $this->order->store($request);

        return $resp;
    }

    public function showLicense($request)
    {
        $resp = $this->order->show($request);
        return $resp->order_uid == auth()->user()->id ? $resp : null;
    }

}
