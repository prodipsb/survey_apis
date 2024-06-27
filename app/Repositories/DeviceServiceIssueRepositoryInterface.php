<?php 

namespace App\Repositories;

use App\Models\DeviceServiceIssue;

interface DeviceServiceIssueRepositoryInterface{

    public function create( array $data) : DeviceServiceIssue;

    public function getDeviceIssues($request);

    public function show($id);

    public function edit($id);

    public function update(array $data, int $id);

    public function destroy(int $id);

}