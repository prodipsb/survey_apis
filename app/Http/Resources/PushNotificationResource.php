<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PushNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"=> $this->id,
            "sender_name" => $this->sender_name,
            "receiver_name"=> $this->receiver_name,
            "message_title"=> $this->message_title,
            "message"=> $this->message,
            "read" => $this->read_at ? 'Yes' : 'No',
            "read_at" => $this->read_at ? Carbon::parse($this->read_at)->format("d-m-Y h:i:s A") : '',
            "created_at" => Carbon::parse($this->created_at)->format("d-m-Y"),
        ];
    }
}
