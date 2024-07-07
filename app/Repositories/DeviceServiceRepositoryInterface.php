<?php 

namespace App\Repositories;

use App\Models\DeviceService;

interface DeviceServiceRepositoryInterface{

    public function create( array $data) : DeviceService;

    public function getDeviceServices($request);

    public function getSelfDeviceServices($request);

    public function getReadyDevicesForAO($request);

    public function deliveredDevice($request);

    public function show($id);

    public function edit($id);

    public function update(array $data, int $id);

    public function destroy(int $id);

}