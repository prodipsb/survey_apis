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

    public function user()
    {
      return  $this->belongsTo(User::class);
    }

    public function role()
    {
      return  $this->belongsTo(Role::class, 'role_id');
    }

    public function supervisor()
    {
      return  $this->hasOne(User::class, 'id', 'supervisor_user_id');
    }


    public function scopeProcess($query)
    {

      if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'executive'){
        return $query;
        
      }else{
        
        $userIds =  User::where('supervisor_user_id', auth()->user()->id)->pluck('id')->toArray();
        if(auth()->user()->user_type == 'territory_manager'){
          $userIds =  User::whereIn('supervisor_user_id', $userIds)->pluck('id')->toArray();

        }else{
          $userIds =  User::whereIn('supervisor_user_id', $userIds)->pluck('id')->toArray();
          $userIds =  User::whereIn('supervisor_user_id', $userIds)->pluck('id')->toArray();
        }
       
        $userIds[] = auth()->user()->id;
        return $query->whereIn('user_id', $userIds);
   
      }
    
    }


}
