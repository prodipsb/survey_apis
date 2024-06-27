<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        //dd($this->user);
        return [
            "id"=> $this->id,
            "notifiable_id" => $this->notifiable_id,
            "survey_id"=> array_key_exists('survey_id', $this->data) ? $this->data['survey_id'] : '',
            "submitted_user"=> array_key_exists('submitted_user', $this->data) ? $this->data['submitted_user'] : '',
           // "submitted_email"=> $this->user->email,
           // "submitted_phone"=> $this->user->phone,
            "message"=> array_key_exists('message', $this->data) ? $this->data['message'] : '',
            "read" => $this->read_at ? 'Yes' : 'No',
            "read_at" => $this->read_at ? Carbon::parse($this->read_at)->format("Y-m-d h:i:s A") : '',
            "created_at" => Carbon::parse($this->created_at)->format("Y-m-d h:i:s A"),
        ];
    }
}
