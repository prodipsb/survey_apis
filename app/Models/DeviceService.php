<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceService extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'binNumber',
        'device_id',
        'device_serial_number',
        'comment',
        'status'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id')->select('id', 'name');
    }

    public function details(){
        return $this->belongsTo(BINInformation::class, 'device_serial_number', 'serialNumber')->select('id', 'binNumber', 'serialNumber', 'device', 'outletName', 'outletAddress');
    }

    public function issue(){
        return $this->belongsTo(DeviceServiceIssue::class, 'deviceIssue')->select('id', 'title');
    }

    public function circle(){ 
        return $this->hasOne(DeviceServiceCircle::class, 'device_service_id', 'id')->latest();
        // return $this->hasMany(DeviceServiceCircle::class, 'device_service_id', 'id')->latest();
    }

    public function device(){ 
        return $this->hasOne(Device::class, 'user_id', 'user_id')->select('user_id', 'user', 'device_token')->latest();
    }

    public function issues(){ 
        return $this->hasMany(ServiceIssue::class, 'service_id');
    }

    
}
