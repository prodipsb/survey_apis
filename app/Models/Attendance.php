<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role_id',
        'employee_id',
        'supervisor_user_id',
        'date',
        'in_latitude',
        'in_longitude',
        'in_location',
        'out_latitude',
        'out_longitude',
        'out_location',
        'in_json_location',
        'out_json_location'
    ];
}
