<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SurveyResource extends JsonResource
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
            'id'=> $this->id,
            'date'=> $this->date,
            'user_id'=> $this->user_id,
            'role_id'=> $this->role_id,
            'submitted_user_name' => $this->user()->first()->name,
            'submitted_user_mobile' => $this->user()->first()->mobile,
            'binHolderName' => $this->binHolderName,
            'binHolderEmail' => $this->binHolderEmail,
            'binHolderMobile' => $this->binHolderMobile,
            'division' => $this->division,
            'subDivision' => $this->subDivision,
            'circle' => $this->circle,
            'shopName' => $this->shopName,
            'brandname' => $this->brandname,
            'businessRegisteredAddress' => $this->businessRegisteredAddress,
            'outletAddress' => $this->outletAddress,
            'category' => $this->category,
            'subCategory' => $this->subCategory,
            'numberOfOutlet' => $this->numberOfOutlet,
            'numberOfCounter' => $this->numberOfCounter,
            'transactionType' => $this->transactionType,
            'monthlyAverageSales' => $this->monthlyAverageSales,
            'monthlyAverageCustomer' => $this->monthlyAverageCustomer,
            'onlineSaleAvailable' => $this->onlineSaleAvailable,
            'onlineSaleParcent' => $this->onlineSaleParcent,
            'onlineOrderMode' => $this->onlineOrderMode,
            'productInfo' => $this->productInfo,
            'productName' => $this->productName,
            'productUnit' => $this->productUnit,
            'unitPrice' => $this->unitPrice,
            'vatParcent' => $this->vatParcent,
            'sdPercent' => $this->sdPercent,
            'priceIncludingVat' => $this->priceIncludingVat,
            'priceExcludingVat' => $this->priceExcludingVat,
        ];
        
    }



}
