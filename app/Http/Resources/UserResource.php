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
            'name' => $this->name ?? '',
            'phone'=> $this->phone ?? '',
            'email' => $this->email ?? '',
            'role' => $this->role->name ?? '',
            'supervisor' => $this->supervisor->name ?? '',
            'reporting_to' => $this->reportTo->name ?? '',
            'location' => $this->location ?? '',
            'city' => $this->city ?? '',
            'division' => $this->division ?? '',
            'last_login' => $this->last_login ?? '',
            'last_logout' => $this->last_logout ?? '',
            'date_of_joining' => $this->date_of_joining ?? '',
            'status' => $this->status ?? ''

        ];
    }
}
