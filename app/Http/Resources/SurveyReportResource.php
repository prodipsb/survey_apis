<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SurveyReportResource extends JsonResource
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
            'date'=> Carbon::parse($this->created_at)->format('d-m-Y h:i A'),
            'employee_id'=> $this->user->employee_id,
            'surveySubmittedUserName' => $this->surveySubmittedUserName,
            'surveySubmittedUserPhone' => $this->surveySubmittedUserPhone,
            'role' => $this->role->name,
            'supervisor' => $this->supervisor,
            'binNumber' => $this->binNumber,
            'binHolderName' => $this->binHolderName,
            'binHolderMobile' => $this->binHolderMobile,
            'division' => $this->division,
            'circle' => $this->circle,
            'shopName' => $this->shopName,
            'businessRegisteredAddress' => $this->businessRegisteredAddress,
            'outletAddress' => $this->outletAddress,
            'category' => $this->category,
            'subCategory' => $this->subCategory,
            'transactionType' => $this->transactionType,
            'onlineSaleAvailable' => $this->onlineSaleAvailable,
            'weeklyHoliday' => $this->weeklyHoliday,
        ];
        
    }



}
