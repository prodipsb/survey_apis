<?php

namespace App\Http\Controllers;

use App\Http\Traits\GlobalTraits;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
{
    use GlobalTraits;

    protected $model = 'Device';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }




    public function storeDeviceToken(Request $request)
    {
        $inputs = $request->all();
       // dd($inputs);

        $rules = [
            'user_id' => 'required',
            'user' => 'required',
            'device_token' => 'required',
        ];
        

        $validation = Validator::make($inputs, $rules);

        if ($validation->fails()) {

            return $this->throwMessage(422, 'error', $validation->errors()->first());
        }

        $isExist = $this->checkIfExistDeviceToken($request->device_token);
        if ($isExist) {
            return $this->throwMessage(200, 'success', 'Device Token already Stored!');
        }

        try {

            $this->storeData($request, $this->model);
            $message = 'Device token Saved Successfully';
            return $this->throwMessage(200, 'success', 'Device token ', $message);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }


    public function getDeviceTokens()
    {
        try {

            $deviceTokens = Device::all();
            return $this->throwMessage(200, 'success', 'Device tokens', $deviceTokens);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }



    public function checkIfExistDeviceToken($deviceToken)
    {
        $token = Device::where('device_token', $deviceToken)->first();
        return $token;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Device  $device
     * @return \Illuminate\Http\Response
     */
    public function deleteDeviceToken(Request $request)
    {
        try {
            Device::findOrFail($request->id)?->delete();
            return $this->throwMessage(200, 'success', 'Device token deleted successfully');
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', 'Device token not found');
        }
    }
}
