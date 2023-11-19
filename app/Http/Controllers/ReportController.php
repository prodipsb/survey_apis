<?php

namespace App\Http\Controllers;

use App\Http\Resources\SurveyResource;
use App\Http\Traits\GlobalTraits;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected $model = 'Survey';
    protected $limit = 10; 

    use GlobalTraits;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function masterReport(Request $request)
    {
        try {
            $data = [];
            $listData = $this->getModel($this->model)::process();
            $listData = $listData->with('surveyItems');


            if($request->has('search')) {

                 $listData = $listData->when(request('search'), function ($query, $search) {
                    $query->whereFullText([
                        'binHolderName',
                        'binHolderMobile',
                        'binHolderEmail',
                        'shopName',
                        'brandName',
                        'productName',
                        'surveySubmittedUserName',
                        'surveySubmittedUserEmail',
                        'surveySubmittedUserPhone'
                    ], $search);
                });
            }


                // $listData = $listData::search($request->search);

                // $listData = $listData->when(request('search'), function ($query, $search) {
                //     $query->whereFullText([
                //         'binHolderName',
                //         'binHolderMobile',
                //         'binHolderEmail',
                //         'shopName',
                //         'brandName',
                //         'productName',
                //     ], $search);
                // });
           // }


            // if($request->search){
            //     $listData = $listData->where('name', 'like', '%'.$request->search.'%');
            // }
            if($request->status){
                    $listData = $listData->where('type', $request->status);
            }
            if($request->course){
                $listData = $listData->where('division', $request->course);
            }
            
            if ($request->has('start_date') && $request->has('end_date')) {
                $listData = $listData->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }

           
            
            if ($request->has('export') && $request->get('export') == true) {
                $listData = SurveyResource::collection($listData->get());
                $fields = [
                    'id',
                    'date',
                    'user_id',
                    'role_id',
                    'surveySubmittedUserName',
                    'surveySubmittedUserEmail',
                    'surveySubmittedUserPhone',
                    'binHolderName', 
                    'binHolderEmail',
                    'binHolderMobile',
                    'division',
                    'subDivision',
                    'circle',
                    'shopName',
                    'brandname',
                    'businessRegisteredAddress',
                    'outletAddress',
                    'category',
                    'subCategory',
                    'numberOfOutlet',
                    'numberOfCounter',
                    'transactionType',
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
                    'priceExcludingVat'

                ];
                $listData = $listData->toArray($fields);

                
                return $this->csvExport($listData, $fields, '/uploads/reports');
            }
            $listData = $listData->orderBy('created_at', 'desc');
            
            $listData = $listData->paginate($this->limit);
           // dd($listData);
            return SurveyResource::collection($listData);

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
       // return $this->throwMessage(200, 'success', 'All the list of Lessons ', $data);

    }




    public function performaceReport(Request $request){
        

        try {

            $listData = $this->getModel($this->model)::select('user_id', DB::raw('date, surveySubmittedUserName, surveySubmittedUserEmail, surveySubmittedUserPhone, surveySubmittedUserAvatar, count(user_id) as totalSurvey, sum(unitPrice) as totalUniPrice, sum(vatParcent) as totalVat, sum(sdPercent) as totalSdPercent, sum(priceIncludingVat) as totalPriceIncludingVat, sum(priceExcludingVat) as totalPriceExcludingVat, created_at'))->process();

            $listData = $listData->with('user');

            if($request->has('search')) {

                $listData = $listData->when(request('search'), function ($query, $search) {
                   $query->whereFullText([
                        'binHolderName',
                        'binHolderMobile',
                        'binHolderEmail',
                        'shopName',
                        'brandName',
                        'productName',
                       'surveySubmittedUserName',
                       'surveySubmittedUserEmail',
                       'surveySubmittedUserPhone'
                   ], $search);
               });
           }
           
            // if($request->search){
            //     $listData = $listData->where('name', 'like', '%'.$request->search.'%');
            // }
            if($request->user_id){
                    $listData = $listData->where('user_id', $request->user_id);
            }
           
            
            if ($request->has('start_date') && $request->has('end_date')) {
                $listData = $listData->whereBetween('created_at', [$request->start_date, $request->end_date]);
            }
            

            $listData = $listData->groupBy('user_id');

            $listData = $listData->orderBy('created_at', 'desc');
            
            $listData = $listData->paginate($this->limit);
            

            return $this->throwMessage(200, 'success', 'performance data', $listData);


        } catch (\Exception $e) {
            return $this->throwMessage(402, 'error', $e->getMessage());
        }

    }












    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
