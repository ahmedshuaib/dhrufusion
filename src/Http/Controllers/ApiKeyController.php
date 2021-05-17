<?php

namespace TFMSoftware\DhruFusion\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TFMSoftware\DhruFusion\Models\DhruFusion;

class ApiKeyController extends Controller
{

    public function __construct()
    {
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $infos = DhruFusion::query();
        return $infos;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'api_key' => 'required|unique:dhru_fusions,api_key'
        ]);

        $fusion = new DhruFusion();
        $fusion->username = auth()->user()->username;
        $fusion->user_id = auth()->id();
        $fusion->api_key = $request->input('api_key');
        $fusion->save();
        return response()->json(['msg' => 'Api key successfully created'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $fusion = DhruFusion::findorfail($id);
        return $fusion;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'api_key' => 'required|unique:dhru_fusions,api_key'
        ]);

        $fusion = DhruFusion::findorfail($id);
        $fusion->api_key = $request->input('api_key');
        $fusion->save();
        return response()->json(['msg' => 'Api key successfully updated'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fusion = DhruFusion::findorfail($id);
        $fusion->delete();
        return response()->json(['msg' => 'Api key deleted!'], 200);
    }
}
