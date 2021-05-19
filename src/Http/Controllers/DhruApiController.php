<?php

namespace TFMSoftware\DhruFusion\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use TFMSoftware\DhruFusion\Models\DhruFusion;

class DhruApiController extends Controller
{
    public function dhru_login(Request $request) {

        $request->validate([
            'username' => 'required',
            'key'    => 'required'
        ]);

        $key = DhruFusion::findorfail($request->key);

        Auth::loginUsingId($key->user_id);

        return Auth::check();
    }

    public function account_info() {
        return Auth::user();
    }

    public function license_order(Request $request) {

        $request->validate([
            'package_id' => 'required',
            'email' => 'required'
        ]);

        $user = User::where('email', $request->email)->firstorfail();

        $response = Http::post('https://www.tfmtool.com/admin/api/orders', [
            'package_id' => $request->package_id,
            'user_id'   => $user->id,
        ]);

        return $response;
    }

    public function credit_order(Request $request) {

        $request->validate([
            'amount' => 'required',
            'email' => 'required'
        ]);

    }



}
