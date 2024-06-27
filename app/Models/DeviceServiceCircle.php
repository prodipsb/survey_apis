<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceServiceCircle extends Model
{
    use HasFactory;

    protected $table="device_service_circles";

    protected $fillable = [
        'device_service_id',
        'comment',
        'status'
    ];


    
}
