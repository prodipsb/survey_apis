<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone'=> $this->phone,
            'user_type' => $this->user_type,
            'gender' => $this->gender,
            'bio' => $this->bio,
            'date_of_joining' => $this->date_of_joining,
            'country' => $this->country,
            'city' => $this->city,
            'division' => $this->division,
            'location' => $this->location,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
            'last_login' => $this->last_login,
            'last_logout' => $this->last_logout,
            'status' => $this->status

        ];
    }
}
