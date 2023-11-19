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
            'surveySubmittedUserName' => $this->surveySubmittedUserName,
            'surveySubmittedUserEmail' => $this->surveySubmittedUserEmail,
            'surveySubmittedUserPhone' => $this->surveySubmittedUserPhone,
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
            'posSoftwareProvider' => $this->posSoftwareProvider,
            'nrbApproved' => $this->nrbApproved,
            'monthlyAverageSales' => $this->monthlyAverageSales,
            'monthlyAverageCustomer' => $this->monthlyAverageCustomer,
            'thirdPartyName' => $this->thirdPartyName,
            'onlineSaleAvailable' => $this->onlineSaleAvailable,
            'onlineSaleParcent' => $this->onlineSaleParcent,
            'onlineOrderMode' => $this->onlineOrderMode,
            'mushak' => $this->mushak,
            'productInfo' => $this->productInfo,
            'productName' => $this->productName,
            'productUnit' => $this->productUnit,
            'unitPrice' => $this->unitPrice,
            'vatParcent' => $this->vatParcent,
            'sdPercent' => $this->sdPercent,
            'priceIncludingVat' => $this->priceIncludingVat,
            'priceExcludingVat' => $this->priceExcludingVat,
            'stockKeeping' => $this->stockKeeping,
            'posSoftware' => $this->posSoftware,
            'posPrinter' => $this->posPrinter,
            'pcOrLaptop' => $this->pcOrLaptop,
            'router' => $this->router,  
            'networking' => $this->networking,
            'surveillance' => $this->surveillance,
            'mobileOperator' => $this->mobileOperator,
            'operatorCoverage' => $this->operatorCoverage,
            'shopPic' => $this->shopPic,
            'binCertificate' => $this->binCertificate,
            'serveyItemList' => $this->surveyItems,
        ];
        
    }



}
