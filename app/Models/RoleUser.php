<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RoleUser extends Model
{
    use HasFactory;

    protected $table="role_user";

    // public function users(){
    //     return $this->belongsTo(Role::class, 'role_id');
    // }


}
