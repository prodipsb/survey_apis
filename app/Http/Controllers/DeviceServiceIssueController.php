<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceServiceIssueRequest;
use App\Http\Traits\GlobalTraits;
use App\Models\DeviceServiceIssue;
use App\Repositories\DeviceServiceIssueRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DeviceServiceIssueController extends Controller
{
    use GlobalTraits;

    public function __construct(public DeviceServiceIssueRepositoryInterface $deviceServiceIssueRepository)
    {
    }

    public function index(Request $request)
    {
        return $this->deviceServiceIssueRepository->getDeviceIssues($request);
    }


    public function store(DeviceServiceIssueRequest $request)
    {

        $data = $request->validated();
        $deviceIssue = $this->deviceServiceIssueRepository->create($data);
        return $this->throwMessage(200, 'success', 'Device issue saved!', $deviceIssue);
    }

    public function show($id)
    {
        return $this->deviceServiceIssueRepository->show($id);
    }


    public function edit(DeviceServiceIssue $deviceServiceIssue)
    {
        return $this->throwMessage(200, 'success', 'Device issue', $deviceServiceIssue);
    }

    public function update(DeviceServiceIssueRequest $request)
    {

        $data = $request->validated();
        $deviceIssue = $this->deviceServiceIssueRepository->update($data, $request->id);

        return $this->throwMessage(200, 'success', 'Device issue updated!', $deviceIssue);
    }

    public function destroy($id)
    {
        return $this->deviceServiceIssueRepository->destroy($id);
    }
}
