<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeviceServiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'binNumber' => 'required|string',
            'deviceIssues' => 'required|string',
            'device_id' => 'required',
            'device_serial_number' => 'required',
            'date' => 'sometimes',
            'status' => 'sometimes',
            'comment' => 'sometimes'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'errors' => $validator->errors()
        ], 422));
    }
}

