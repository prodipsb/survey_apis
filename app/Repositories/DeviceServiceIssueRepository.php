<?php

namespace App\Repositories;

use App\Http\Resources\DeviceIssueResource;
use App\Http\Traits\GlobalTraits;
use App\Models\DeviceServiceIssue;
use Illuminate\Http\Request;

class DeviceServiceIssueRepository implements DeviceServiceIssueRepositoryInterface
{

    use GlobalTraits;

    protected $limit = 10;

    public function __construct(private DeviceServiceIssue $model)
    {
    }


    public function create($data): DeviceServiceIssue
    {
        $deviceServiceIssue = $this->model->create($data);
        return $deviceServiceIssue;
    }

    public function getDeviceIssues($request)
    {
        if($request->search){
            $data = $this->model->where('title', 'LIKE', '%'.$request->search.'%')->paginate($this->limit);

        }else{
            $data = $this->model->paginate($this->limit);
        }
        $deviceIssues = DeviceIssueResource::collection( $data );
        return $deviceIssues;
    }

    public function show($id)
    {
        try {
            $deviceServiceIssue = $this->model->findOrFail($id);
            return $this->throwMessage(200, 'success', 'Device service issue details', $deviceServiceIssue);
        } catch (\Exception $e) {
            return $this->throwMessage(404, 'error', 'Device service issue not Found', $e->getMessage());
        }

    }

    public function edit($id)
    {
        $deviceServiceIssue = $this->model->findOrFail($id);
        return $deviceServiceIssue;
    }

    public function update($data, $id)
    {
        $deviceServiceIssue = $this->model->find($id)->update($data);
        return $deviceServiceIssue;
    }

    public function destroy($id)
    {
        try {
            $deviceServiceIssue = $this->model->findOrFail($id)->delete();
            return $this->throwMessage(200, 'success', 'Device service issue deleted!', $deviceServiceIssue);
        } catch (\Exception $e) {
            return $this->throwMessage(404, 'error', 'Device service issue not Found', $e->getMessage());
        }

    }
}
