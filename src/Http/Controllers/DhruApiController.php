<?php

namespace TFMSoftware\DhruFusion\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TFMSoftware\DhruFusion\Models\DhruFusion;
use App\Helper\Option\OptionHelperFacades as opt;

class DhruApiController extends Controller
{

    public $site_url = "http://test.ahmedshuaib.com";

    public function dhru_login(Request $request)
    {

        $request->validate([
            'username' => 'required',
            'key'    => 'required'
        ]);

        $key = DhruFusion::where('api_key', $request->key)->firstorfail();

        Auth::loginUsingId($key->user_id);

        if (auth()->user()->is_admin || auth()->user()->is_system) {
            $msg = [
                'success' => true,
            ];
        } else {
            $msg = [
                'success' => false
            ];
        }

        return response($msg, 200);
    }

    public function account_info(Request $request)
    {

        $this->dhru_login($request);


        return response()->json([
            'account' => [
                'name' => auth()->user()->username,
                'email' => auth()->user()->email,
                'currency' => opt::system()['currency_suffix'],
                'balance' => auth()->user()->balance . ' ' . opt::system()['currency_suffix']
            ]
        ]);
    }

    public function email_to_id(Request $request)
    {
        $request->validate([
            'email' => 'required'
        ]);
        $user = User::where('email', $request->email)->firstorfail();
        return response(['uid' => $user->id], 200);
    }

    public function license_order(Request $request)
    {

        $request->validate([
            'package_id' => 'required',
            'email' => 'required'
        ]);

        $request->is_active = true;

        $user = User::where('email', $request->email)->firstorfail();
    }

    public function credit_order(Request $request)
    {

        $request->validate([
            'amount' => 'required',
            'email' => 'required'
        ]);
    }
}
