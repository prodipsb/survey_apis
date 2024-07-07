<?php 

// app/Models/Role.php
namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    // Additional customization if needed

    public function user(){   
        return $this->hasMany(RoleUser::class, 'role_id');
    }
}
