<?php

namespace TFMSoftware\DhruFusion\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use TFMSoftware\DhruFusion\Helpers\Responses\DhruResponse;
use TFMSoftware\DhruFusion\Models\DhruFusion;
use Illuminate\Support\Facades\Auth;

class DhruAuth
{
    protected $response;

    public function __construct(DhruResponse $response)
    {
        $this->response = $response;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!$request->has('apiaccesskey')) {
            return $this->response->errorResponse('API Access Key is required!');
        }
        $dhruFusion = DhruFusion::where('api_key', $request->apiaccesskey)->where('is_active', true)->first();
        if(!$dhruFusion) {
            return $this->response->errorResponse('Authentication Failed!');
        }
        Auth::loginUsingId($dhruFusion->user_id);
        return $next($request);
    }
}
