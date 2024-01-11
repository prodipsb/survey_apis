<?php

namespace App\Models;

use App\Events\SurveyNotificationEvent;
use App\Http\Traits\FullTextSearch;
use App\Notifications\SurveyNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class Survey extends Model
{
    use HasFactory;
    use FullTextSearch;

    protected $fillable = [

            'user_id',
            'role_id',
            'surveySubmittedUserName',
            'surveySubmittedUserEmail',
            'surveySubmittedUserPhone',
            'surveySubmittedUserAvatar',
            'binHolderName',
            'binHolderMobile',
            'binHolderEmail',
            'binHolderNid',
            'binNumber',
            'commissioneRate',
            'date',
            'businessStartDate',
            'division',
            'subDivision',
            'circle',
            'shopName',
            'brandName',
            'areaOrshoppingMall',
            'businessRegisteredAddress',
            'outletAddress',
            'category',
            'subCategory',
            'latitude',
            'longitude',
            'numberOfOutlet',
            'numberOfCounter',
            'differentBin',
            'transactionType',
            'posSoftwareProvider',
            'nrbApproved',
            'thirdPartyName',
            'monthlyAverageSales',
            'monthlyAverageCustomer',
            'onlineSaleAvailable',
            'onlineSaleParcent',
            'onlineOrderMode',
            'productInfo',
            'productName',
            'productUnit',
            'unitPrice',
            'vatParcent',
            'sdPercent',
            'priceIncludingVat',
            'priceExcludingVat',
            'stockKeeping',
            'posSoftware',
            'posPrinter',
            'pcOrLaptop',
            'mushak',
            'router',
            'networking',
            'surveillance',
            'mobileOperator',
            'operatorCoverage',
            'weeklyHoliday',
            'shopPic',
            'binCertificate'

    ];


    public $searchable = [
      'binHolderName',
      'binHolderMobile',
      'binHolderEmail',
      'division',
      'shopName',
      'brandName',
      'category',
      'productName'
  ];


    protected $dispatchesEvents = [ 'created' => SurveyNotificationEvent::class ];

    protected $appends = ['supervisor', 'reportTo'];


  //   public static function boot() {
  //     parent::boot();
  
  //     static::created(function($message) {
  //         Notification::send($message->user, new SurveyNotification($message));
  //     });
  // }



    public function surveyItems()
    {
      return  $this->hasMany(SurveyItem::class);
    }


    public function user()
    {
      return  $this->belongsTo(User::class);
    }


    public function scopeProcess($query)
    {

      $roles = Role::where('id', '>=', 13)->pluck('id');
      return $query->whereIn('role_id', $roles->toArray());

    }



    public function role()
    {
      return  $this->belongsTo(Role::class, 'role_id');
    }


    public function getSupervisorAttribute()
    {
       $supervisor = User::find($this->user->supervisor_user_id);
       return $supervisor?->name;
    }

    public function getReportToAttribute()
    {
      $reportingTo = User::find($this->user->reporting_user_id);
      return $reportingTo?->name;
    }

    


    
}
