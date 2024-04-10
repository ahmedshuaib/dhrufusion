<?php

namespace TFMSoftware\DhruFusion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DhruRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'action' => 'required|string',
            'parameters' => 'nullable|string',
            'username' => 'required_if:action,accountinfo,imeiservicelist|string',
            'apiaccesskey' => 'required_if:action,accountinfo,imeiservicelist|string',
        ];
    }


    public function messages()
    {
        return [
            'action.required' => 'Action is required',
            'action.string' => 'Action must be a string',
            'parameters.string' => 'Parameters must be a string',
            'username.required_with' => 'Username is required',
            'username.string' => 'Username must be a string',
            'apiaccesskey.required_with' => 'API Access Key is required',
            'apiaccesskey.string' => 'API Access Key must be a string',
        ];
    }

}
