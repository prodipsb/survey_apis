<?php

namespace App\Http\Controllers;

use App\Http\Traits\GlobalTraits;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class SettingController extends Controller
{

    use GlobalTraits;

    protected $model = 'Setting';
    protected $uploadDir = 'uploads/setting';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $settings = Setting::first();
            return $this->throwMessage(200, 'success', 'settings ', $settings);

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogo()
    {
        try {

            $logo = Setting::first()->logo;
            return $this->throwMessage(200, 'success', 'logo ', $logo);

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generalSettingStore(Request $request)
    {
        

        $user = $request->user();

        //Check if this is super admin or not
        if ($user->user_type !== 'admin') {
            return $this->throwMessage(413, 'error', 'Permission not granted, Only Super Admin has the access to register new user');
        }


        $inputs = $request->all();
 

        $rules = [
            'website_title' => 'required'
        ];


        $validation = Validator::make( $inputs, $rules );
    
        if ( $validation->fails() ) { return $validation->errors(); }


        try {

        $setting = Setting::first();

        if(!empty($setting->id)){

           // return $request->file('logo');

            $request->merge(['updated_by' => $this->getAuthID()]);

            $user = $this->updateData($request, $setting->id, $this->model, $exceptFieldsArray = ['logo', 'favicon'], $fileUpload = true, $fileInputName = ['logo', 'favicon'], $path = $this->uploadDir);

            $message = 'Data Updated Successfully';

        }else{

            $request->merge(['created_by' => $this->getAuthID()]);

            $user = $this->storeData($request, $this->model, $fileUpload = true, $fileInputName = ['logo', 'favicon'], $path = $this->uploadDir);

            $message = 'Data Stored Successfully';

        }


        return $this->throwMessage(200, 'success', $message);


        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Setting $setting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
