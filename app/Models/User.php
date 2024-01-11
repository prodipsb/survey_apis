<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Http\Traits\FullTextSearch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    use FullTextSearch;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'password',
        'phone',
        'role_id',
        'supervisor_id',
        'user_type',
        'gender',
        'reporting_role_id',
        'supervisor_user_id',
        'reporting_user_id',
        'bio',
        'bin_no',
        'date_of_joining',
        'country',
        'city',
        'division',
        'location',
        'longitude',
        'latitude',
        'last_login',
        'last_logout',
        'created_by',
        'updated_by',
        'status'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    protected $with = ['role'];

    public $searchable = [
        'name',
        'phone',
        'email',
        'user_type',
        'location'
    ];

    


    public function isSuperAdmin($userType)
    {
        if ($userType == 'admin') {
            return true;
        } else {
            return false;
        }
    }

    public function isAdmin($isAdmin = 0)
    {
        if ($isAdmin) {
            return true;
        } else {
            return false;
        }
    }

    // public function removeRoles()
    // {
    //     $this->roles()->detach();
    // }

    public function updateRoleAndPermissions($role)
    {
        // Remove all existing roles
       // $this->syncRoles([]);
       $this->roles()->detach();


        // Assign the new role
         $this->assignRole($role);

        // dd($role->permissions);

        // // Sync the permissions for the new role
        // $this->syncPermissions($role->permissions);
    }



    public function role(){
        return $this->belongsTo(Role::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function supervisor(){
        return $this->belongsTo(User::class, 'supervisor_user_id');
        // return $this->belongsTo(Role::class, 'supervisor_id');
    }

    public function reportTo(){
        return $this->belongsTo(User::class, 'reporting_user_id');
        // return $this->belongsTo(Role::class, 'reporting_role_id');
    }

    public function permissions(){
        return $this->belongsTo(Permission::class);
    }

    // public function roles() {
    //     return $this->belongsTo('App\Role');
    // }


}
