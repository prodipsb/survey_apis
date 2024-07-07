<?php

namespace App\Repositories;

use App\Http\Resources\DeviceServiceResource;
use App\Http\Traits\GlobalTraits;
use App\Models\DeviceService;
use App\Models\DeviceServiceIssue;

class DeviceServiceRepository implements DeviceServiceRepositoryInterface
{

    use GlobalTraits;

    protected $limit = 20;

    protected $uploadDir = 'uploads/deployment_files';

    public function __construct(private DeviceService $model)
    {
    }



    public function create($data): DeviceService
    {
        // Merge the additional data into the original data array
        $data = array_merge($data, [
            'user_id' => auth()->user()->id,
            'date' => date('Y-m-d'),
            'status' => 'Pending'
        ]);

        // return $this->throwMessage(200, 'success', 'Device service saved!', $data);

        // Decode the deviceIssues from JSON to array
        $deviceIssues = json_decode($data['deviceIssues'], true);

        // Unset the deviceIssues from the data array
        unset($data['deviceIssues']);

        // Create the DeviceService record
        $deviceService = $this->model->create($data);

        // If deviceIssues are present, store them
        if (!empty($deviceIssues)) {
            $this->storeServiceIssues($deviceService->id, $deviceIssues);
        }

        $notificationData = [
            'type' => 'Device Serviceing',
            'sender_id' => auth()->user()->id,
            'sender_name' => auth()->user()->name,
            'receiver_id' => auth()->user()->id,
            'receiver_name' => auth()->user()->name,
            'message_title' => 'Device Service',
            'message' => 'Device Service created',
            'status' => 'Pending'
        ];

       // dd($notificationData);

        $data = $this->sendPushNotification('PushNotification', $notificationData);
        //dd($data);

        return $deviceService;
    }



    // public function create($data): DeviceService
    // { 





    //     $data = array_merge($data, [
    //         'user_id' => auth()->user()->id,
    //         'date' => date('Y-m-d'),
    //         'status' => 'Sent'
    //     ]);

    //     unset($data['deviceIssues']);

    //     // dd($data);
    //     $deviceService = $this->model->create($data);

    //     $deviceIssues=json_decode($data['deviceIssues']);

    //     // dd(json_decode($deviceIssues));

    //     if ($deviceIssues) {
    //       $this->storeServiceIssues($deviceService->id, $deviceIssues);
    //     }



    //     return $deviceService;
    // }

    public function getDeviceServices($request)
    {

        // Start building the query
        $query = $this->model->with(['user', 'issues', 'issue', 'details', 'circle', 'device'])->latest();

        // Add conditions based on user type
        if (auth()->user()->user_type != 'admin') {
            $query->where('user_id', auth()->user()->id);
        }

        if (!empty($request->search)) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        $data = $query->paginate($this->limit);
        // Paginate the results
        $deviceServices = DeviceServiceResource::collection($data);
        return $deviceServices;
        // return $query->paginate($this->limit);

        // if (auth()->user()->user_type == 'admin') {
        //     return $this->model->where('title', 'LIKE', '%' . $request->search . '%')->with(['binDetails', 'deviceIssue'])->latest()->paginate($this->limit);
        // } else {
        //     return $this->model->where('user_id', auth()->user()->id)->with(['binDetails', 'deviceIssue'])->latest()->paginate($this->limit);
        // }
    }

    public function getSelfDeviceServices($request)
    {

        $data =  $this->model->where('user_id', auth()->user()->id);
        if($request->binNumber){
            $data =  $data->where('binNumber', $request->binNumber);
        }
        $data =  $data->with(['details', 'issues']);
        $data = $data->orderBy('updated_at', 'desc');
        return $data->paginate($this->limit);
    }

    public function show($id)
    {
        try {
            $deviceService = $this->model->findOrFail($id);
            return $this->throwMessage(200, 'success', 'Device service details', $deviceService);
        } catch (\Exception $e) {
            return $this->throwMessage(404, 'error', 'Device service not Found', $e->getMessage());
        }
    }


    public function getReadyDevicesForAO($request)
    {
        $data =  $this->model->where(['user_id' => auth()->user()->id, 'status' => 'AO Received']);
        if($request->binNumber){
            $data =  $data->where('binNumber', $request->binNumber);
        }
        $data =  $data->with(['details', 'issues']);
        $data = $data->orderBy('updated_at', 'desc');
        return $data->paginate($this->limit);

        //return $this->model->where(['user_id' => auth()->user()->id, 'status' => 'AO Received'])->with(['details', 'issues'])->get();
        // return $this->model->where(['user_id' => auth()->user()->id])->with(['details', 'issues'])->get();
    }


    public function deliveredDevice($request)
    {
        // Find the device service by ID
        $deviceService = $this->model->find($request->device_service_id);

        // Update the status of the device service
        $deviceService->update(['status' => $request->status]);

        // Create a new circle record with the provided data
        return $this->storeData($request, 'DeviceServiceCircle', $fileUpload = true, $fileInputName = ['delivered_image'], $path = $this->uploadDir);
    }



    public function edit($id)
    {
        $deviceService = $this->model->findOrFail($id);
        return $deviceService;
    }

    public function update($data, $id)
    {
        // Find the device service by ID
        $deviceService = $this->model->find($id);

        // Update the status of the device service
        $deviceService->update(['status' => $data['status']]);

        // Create a new circle record with the provided data
        $deviceServiceCircle = $deviceService->circle()->create([
            'comment' => $data['comment'],
            'device_service_id' => $id,
            'status' => $data['status']
        ]);

        return $deviceServiceCircle;
    }


    public function destroy($id)
    {
        try {
            $deviceService = $this->model->findOrFail($id)->delete();
            return $this->throwMessage(200, 'success', 'Device service deleted!', $deviceService);
        } catch (\Exception $e) {
            return $this->throwMessage(404, 'error', 'Device service not Found', $e->getMessage());
        }
    }
}
