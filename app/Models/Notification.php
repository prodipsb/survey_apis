<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    // public function user(){
    //     dd('hkk');
    //     return $this->hasOne(User::class, 'notifiable_id', 'id');
    // }

    public function user()
    {
      return  $this->hasOne(User::class, 'id', 'notifiable_id');
    }



}
