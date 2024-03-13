<?php

namespace App\Http\Controllers;

use App\Http\Resources\SurveyResource;
use App\Http\Traits\GlobalTraits;
use App\Models\Survey;
use App\Models\User;
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
            $listData = Survey::process()->with('surveyItems');
            // return $this->throwMessage(200, 'success', 'All the list of Survey ', $listData->get());

            if ($request->has('search')) {
                $listData->whereFullText([
                    'binHolderName',
                    'binHolderMobile',
                    'shopName',
                    'surveySubmittedUserName',
                ], $request->search);
            }

            if ($request->has('employee_id')) {
                $userIds = User::where('employee_id', $request->employee_id)->pluck('id')->toArray();
                $listData->whereIn('user_id', $userIds);
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $listData->whereBetween('date', [$request->start_date, $request->end_date]);
            }

            if ($request->has('role_id')) {
                $listData->where('role_id', $request->role_id);
            }

            if ($request->has('supervise4_user_id')) {
                $userIds = User::where('supervisor_user_id', $request->supervise4_user_id)->pluck('id')->toArray();
                $listData->whereIn('user_id', $userIds)->orWhere('user_id', $request->supervise4_user_id);
            } elseif ($request->has('supervise3_user_id')) {
                $userIds = User::where('supervisor_user_id', $request->supervise3_user_id)->pluck('id')->toArray();
                //  return $this->throwMessage(200, 'success', 'All the list of Survey ', $request->all());
                $listData->whereIn('user_id', $userIds)->with('superviseUsers')->orWhere('user_id', $request->supervise3_user_id);
            } elseif ($request->has('supervise2_user_id')) {
                $userIds = User::where('supervisor_user_id', $request->supervise2_user_id)->pluck('id')->toArray();
                $listData->whereIn('user_id', $userIds)->orWhere('user_id', $request->supervise2_user_id);
            } elseif ($request->has('supervise_user_id')) {
                $userIds = User::where('supervisor_user_id', $request->supervise_user_id)->pluck('id')->toArray();
                $userIds = User::whereIn('supervisor_user_id', $userIds)->pluck('id')->toArray();
                $listData->whereIn('user_id', $userIds)->orWhere('user_id', $request->supervise_user_id);
            }

            if ($request->has('export') && $request->export == true) {
                $listData = $listData->orderBy('created_at', 'desc')->get();
                return $this->exportData($listData);
            }

            $listData = $listData->orderBy('created_at', 'desc')->paginate($this->limit);

            return SurveyResource::collection($listData);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    private function exportData($listData)
    {
        $fields = [
            'date',
            'surveySubmittedUserName',
            'surveySubmittedUserEmail',
            'surveySubmittedUserPhone',
            'role',
            'supervisor',
            'binNumber',
            'binHolderName',
            'binHolderEmail',
            'binHolderMobile',
            'division',
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
            'priceExcludingVat',
            'weeklyHoliday',
        ];

        $listDataArray = $listData->map(function ($resource) {
            return $resource->toArray(request());
        });

        $listDataExport = [];
        foreach ($listDataArray as $item) {
            $rowData = [];
            foreach ($fields as $field) {
                $rowData[] = $item[$field] ?? ""; // Use empty string if the field is not present
            }
            $listDataExport[] = $rowData;
        }

        return $this->csvExport($listDataExport, $fields, '/uploads/reports');
    }






    // public function masterReport(Request $request)
    // {
    //     try {
    //         $data = [];
    //         $listData = Survey::process();
    //         $listData = $listData->with('surveyItems');


    //         if($request->has('search')) {

    //              $listData = $listData->when(request('search'), function ($query, $search) {
    //                 $query->whereFullText([
    //                     'binHolderName',
    //                     'binHolderMobile',
    //                     'shopName',
    //                     'surveySubmittedUserName',
    //                 ], $search);
    //             });
    //         }

    //         if ($request->has('employee_id') && $request->has('employee_id')) {
    //             $userIds = User::where('employee_id', $request->employee_id)->pluck('id')->toArray();
    //             $listData = $listData->whereIn('user_id', $userIds);
    //         }

    //         if ($request->has('start_date') && $request->has('end_date')) {
    //             $listData = $listData->whereBetween('date', [$request->start_date, $request->end_date]);
    //         }

    //         if ($request->has('role_id') && $request->has('role_id')) {
    //             $listData = $listData->where('role_id', $request->role_id);
    //         }

    //         if($request->has('supervise4_user_id')){

    //             $userIds = User::where('supervisor_user_id', $request->supervise4_user_id)->pluck('id')->toArray();
    //             $listData = $listData->whereIn('user_id', $userIds)->orWhere('user_id', $request->supervise4_user_id);

    //         }elseif($request->has('supervise3_user_id')){

    //             $userIds = User::where('supervisor_user_id', $request->supervise3_user_id)->pluck('id')->toArray();
    //             $listData = $listData->whereIn('user_id', $userIds);
    //             $listData->with('superviseUsers');

    //         }elseif($request->has('supervise2_user_id')){

    //             $userIds = User::where('supervisor_user_id', $request->supervise2_user_id)->pluck('id')->toArray();
    //             $listData = $listData->whereIn('user_id', $userIds);

    //         }elseif($request->has('supervise_user_id')){

    //             $userIds = User::where('supervisor_user_id', $request->supervise_user_id)->pluck('id')->toArray();
    //             $listData = $listData->whereIn('user_id', $userIds);
    //             $listData->with('superviseUsers');

    //         }


    //         if ($request->has('export') && $request->get('export') == true) {
    //             $listData = SurveyResource::collection($listData->get());
    //             $fields = [
    //                 'date',
    //                 'surveySubmittedUserName',
    //                 'surveySubmittedUserEmail',
    //                 'surveySubmittedUserPhone',
    //                 'role',
    //                 'supervisor',
    //                 'binNumber', 
    //                 'binHolderName', 
    //                 'binHolderEmail',
    //                 'binHolderMobile',
    //                 'division',
    //                 'circle',
    //                 'shopName',
    //                 'brandname',
    //                 'businessRegisteredAddress',
    //                 'outletAddress',
    //                 'category',
    //                 'subCategory',
    //                 'numberOfOutlet',
    //                 'numberOfCounter',
    //                 'transactionType',
    //                 'monthlyAverageSales',
    //                 'monthlyAverageCustomer',
    //                 'onlineSaleAvailable',
    //                 'onlineSaleParcent',
    //                 'onlineOrderMode',
    //                 'productInfo',
    //                 'productName',
    //                 'productUnit',
    //                 'unitPrice',
    //                 'vatParcent',
    //                 'sdPercent',
    //                 'priceIncludingVat',
    //                 'priceExcludingVat',
    //                 'weeklyHoliday',

    //             ];

    //               // Convert each resource to array
    //               $listDataArray = $listData->map(function ($resource) {
    //                 return $resource->toArray(request());
    //             });

    //             // Extract only the specified fields
    //             $listDataExport = [];
    //             foreach ($listDataArray as $item) {

    //                 $rowData = [];
    //                 foreach ($fields as $field) {

    //                     $rowData[] = $item[$field] ? $item[$field] : "null"; // Use empty string if the field is not present
    //                 }
    //                 $listDataExport[] = $rowData;
    //             }


    //             return $this->csvExport($listDataExport, $fields, '/uploads/reports');
    //         }


    //         $listData = $listData->orderBy('created_at', 'desc');

    //     	$listData = $listData->paginate($this->limit);

    //         return SurveyResource::collection($listData);

    //     } catch (\Exception $e) {
    //         return $this->throwMessage(413, 'error', $e->getMessage());
    //     }
    //    // return $this->throwMessage(200, 'success', 'All the list of Survey ', $data);

    // }




    public function performaceReport(Request $request)
    {


        try {

            $listData = $this->getModel($this->model)::select('user_id', DB::raw('date, surveySubmittedUserName, surveySubmittedUserEmail, surveySubmittedUserPhone, surveySubmittedUserAvatar, count(user_id) as totalSurvey, sum(unitPrice) as totalUniPrice, sum(vatParcent) as totalVat, sum(sdPercent) as totalSdPercent, sum(priceIncludingVat) as totalPriceIncludingVat, sum(priceExcludingVat) as totalPriceExcludingVat, created_at'))->process();

            $listData = $listData->with('user');

            if ($request->has('search')) {

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
            if ($request->user_id) {
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
