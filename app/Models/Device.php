<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user',
        'device_token'
        
    ];

    public function role(){
        return $this->belongsTo(Role::class, 'role_id');
    }


}
