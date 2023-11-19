<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_title', 
        'address', 
        'about', 
        'logo', 
        'favicon',
        'created_by',
        'updated_by'
    ];
}
