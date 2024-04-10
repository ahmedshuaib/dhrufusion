<?php

namespace TFMSoftware\DhruFusion\Services;
use App\Helper\Option\OptionHelperFacades as opt;


class AccountInfoService
{

    public function accountInfo()
    {
        return [
            'message' => 'Your Accout Info',
            'AccoutInfo' => [
                'credit' => auth()->user()->balance . ' ' . $this->getCurrencySuffix(),
                'mail' => auth()->user()->email,
                'currency' => $this->getCurrencySuffix(),
            ],
        ];
    }

    protected function getCurrencySuffix() {
        return opt::system()['currency_suffix'];
    }
}
