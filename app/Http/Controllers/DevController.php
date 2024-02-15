<?php

namespace App\Http\Controllers;

use App\Http\Traits\GlobalTraits;
use App\Models\Device;
use Illuminate\Http\Request;

class DevController extends Controller
{
    use GlobalTraits;

    public function index(){

        $userDevices = Device::with('userInfo')->get();

        foreach($userDevices as $userDevice) {
            // Assuming that the role_id is in the users table
            $userDevice->role_id = $userDevice?->userInfo?->role_id;
            $userDevice->save();
        }

        return $this->throwMessage(200, 'success', 'User Device Role Updated');


    }
}
