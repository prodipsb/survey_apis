<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceServiceResource extends JsonResource
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
            "id" => $this->id,
            "date"=>  Carbon::parse($this->created_at)->format('d m Y h:i A'),
            "user"=>  $this->user?->name,
            "binNumber"=>  $this->binNumber,
            "serialNumber"=>  $this->details?->serialNumber,
            "device"=>  $this->details?->device,
            "outletName"=>  $this->details?->outletName,
            "outletAddress"=>  $this->details?->outletAddress,
            "comment"=>  $this->comment,
            "status"=>  $this->status,
            "lastComment"=>  $this->circle?->comment,
            "updatedAt"=>  Carbon::parse($this->updated_at)->format('d m Y h:i A'),
            "deviceIssues"=>  $this->issues,
            "deviceToken"=>  $this->device?->device_token,
        ];
    }
}
