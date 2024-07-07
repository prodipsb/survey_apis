<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceServiceIssueRequest;
use App\Http\Requests\DeviceServiceRequest;
use App\Http\Traits\GlobalTraits;
use App\Models\DeviceService;
use App\Models\DeviceServiceIssue;
use App\Repositories\DeviceServiceIssueRepositoryInterface;
use App\Repositories\DeviceServiceRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DeviceServiceController extends Controller
{
    use GlobalTraits;

    public function __construct(public DeviceServiceRepositoryInterface $deviceServiceRepository)
    {
    }

    public function index(Request $request)
    {
        return $this->deviceServiceRepository->getDeviceServices($request);
    }

    public function getUserDeviceServices(Request $request)
    {
        return $this->deviceServiceRepository->getSelfDeviceServices($request);
    }

    public function readyDevicesForAO(Request $request)
    {
        return $this->deviceServiceRepository->getReadyDevicesForAO($request);
    }

    public function deliveredDevice(Request $request)
    {

        $response = $this->deviceServiceRepository->deliveredDevice($request);
        return $this->throwMessage(200, 'success', 'Device delivered successfully!', $response);
    }

    

    


    public function store(DeviceServiceRequest $request)
    {
        // return $this->throwMessage(200, 'success', 'Device service saved!', $request->all());

        $data = $request->validated();
        //  return $this->throwMessage(200, 'success', 'Device service saved!', $data);
        $deviceService = $this->deviceServiceRepository->create($data);
        return $this->throwMessage(200, 'success', 'Device service saved!', $deviceService);
    }



    public function show($id)
    {
        return $this->deviceServiceRepository->show($id);
    }


    public function edit(DeviceService $deviceService)
    {
        return $this->throwMessage(200, 'success', 'Device service', $deviceService);
    }

    public function update(Request $request)
    {

        // $data = $request->validated();
        $deviceIssue = $this->deviceServiceRepository->update($request->all(), $request->id);
        return $this->throwMessage(200, 'success', 'Device service updated!', $deviceIssue);
    }

    public function destroy($id)
    {
        return $this->deviceServiceRepository->destroy($id);
    }
}
