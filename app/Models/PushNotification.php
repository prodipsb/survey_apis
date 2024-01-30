<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    use HasFactory;

    protected $table="push_notifications";

    protected $fillable = [
        'type',
        'sender_id',
        'sender_name',
        'receiver_id',
        'receiver_name',
        'sender_role_id',
        'sender_role_name',
        'receiver_role_id',
        'receiver_role_name',
        'notification_title',
        'notification_message',
        'read_at'
        
    ];
}
