<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BINInformation extends Model
{
    use HasFactory;

    protected $table="bin_information";

    protected $fillable=[
        'serialNumber',
        'binNumber',
        'device',
        'outletName',
        'outletAddress'
    ];
}
