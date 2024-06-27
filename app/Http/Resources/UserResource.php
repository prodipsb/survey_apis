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
        return [
            'id' => $this   ->id,
            'employee_id' => $this->employee_id,
            'name' => $this->name ?? '',
            'email' => $this->email ?? '',
            'phone'=> $this->phone ?? '',
            'role' => $this->role->name ?? '',
            'supervisor' => $this->supervisorRole->name ?? '',
            'supervisor_name' => $this->supervisor->name ?? '',
            'gender' => $this->gender ?? '',
            'date_of_joining' => $this->date_of_joining ?? '',
            'country' => $this->country ?? '',
            'zone' => $this->zone ?? '',
            'commissionerate' => $this->commissionerate ?? '',
            'division' => $this->division ?? '',
            'circle' => $this->circle ?? '',
            'address' => $this->address ?? '',
            'status' => $this->status ?? ''

        ];
    }
}
