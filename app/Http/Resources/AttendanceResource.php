<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'date'=> Carbon::parse($this->date)->format('d-m-Y'),
            'employee_id'=> $this->user->employee_id,
            'user_name' => $this->user?->name,
            'user_phone' => $this->user?->phone,
            'role' => $this->role?->name,
            'supervisor' => $this->supervisor?->name,
            'in_time' => Carbon::parse($this->created_at)->format('d-m-Y h:i A'),
            'in_location' => $this->in_location,
            'out_time' =>   $this->created_at == $this->updated_at ? 'N\A' : Carbon::parse($this->updated_at)->format('d-m-Y h:i A'),
            'out_location' => $this->out_location,
            'in_latitude' => $this->in_latitude,
            'in_longitude' => $this->in_longitude,
            'out_latitude' => $this->out_latitude,
            'out_longitude' => $this->out_longitude,
            'in_json_location' => $this->in_json_location,
            'out_json_location' => $this->out_json_location
        ];
        
    }



}
