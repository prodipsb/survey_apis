<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttendanceResource;
use App\Http\Resources\SurveyReportResource;
use App\Http\Resources\SurveyResource;
use App\Http\Traits\GlobalTraits;
use App\Models\Attendance;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ReportController extends Controller
{
    protected $model = 'Survey';
    protected $limit = 20;

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
            'Date',
            'Employee ID',
            'Name',
            'Email',
            'Mobile Number',
            'Role',
            'Supervisor',
            'BIN Number',
            'BIN Holder',
            'BIN Holder Email',
            'BIN Holder Mobile',
            'CommissioneRate',
            'Division',
            'Circle',
            'ShopName',
            'BrandName',
            'Business Registered Address',
            'Outlet Address',
            'Category',
            'Sub Category',
            'Number Of Outlet',
            'Number Of Counter',
            'Transaction Type',
            'POS Software Provider',
            'NBR Approved',
            'Monthly Average Sales',
            'Monthly Average Customer',
            'Third Party Name',
            'Online Sale Available',
            'Online Sale Parcent',
            'Online Order Mode', 
            'Mushak', 
            'Product Info',
            'Product Name',
            'Product Unit',
            'Unit Price',
            'VAT Parcent', 
            'SD Percent',
            'Price Including VAT',
            'Price Excluding VAT',
            'Stock Keeping',
            'POS Software',
            'POS Printer',
            'PC Or Laptop',
            'Router',
            'Networking',
            'Surveillance',
            'Mobile Operator',
            'Operator Coverage',
            'Weekly Holiday',
        ];


        $collection = match ( 'survey' ) {
            'survey' => SurveyReportResource::collection( $listData ),
        };

        $collectionData = $collection->toArray( $fields );

        return $this->csvExport($collectionData, $fields, '/uploads/reports');
    }


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




    public function attendanceReport(Request $request)
    {

        //dd(Auth::user());
        try {
            $listData = Attendance::process()->with(['user', 'role', 'supervisor']);
            // dd($listData->get());


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
                $listData->whereIn('user_id', $userIds)->with('superviseUsers')->orWhere('user_id', $request->supervise3_user_id);
            } elseif ($request->has('supervise2_user_id')) {
                $userIds = User::where('supervisor_user_id', $request->supervise2_user_id)->pluck('id')->toArray();
                $listData->whereIn('user_id', $userIds)->orWhere('user_id', $request->supervise2_user_id);
            } elseif ($request->has('supervise_user_id')) {
                $userIds = User::where('supervisor_user_id', $request->supervise_user_id)->pluck('id')->toArray();
                // $userIds = User::whereIn('supervisor_user_id', $userIds)->pluck('id')->toArray();
                $listData->whereIn('user_id', $userIds)->orWhere('user_id', $request->supervise_user_id);
            }

            if ($request->has('export') && $request->export == true) {
                $listData = $listData->orderBy('created_at', 'desc')->get();
                return $this->exportAttendanceData($listData);
            }

            $listData = $listData->orderBy('created_at', 'desc')->paginate($this->limit);

            return AttendanceResource::collection($listData);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }


    private function exportAttendanceData($listData)
    {
        $fields = [
            'Date',
            'Employee ID',
            'Name',
            'Mobile Number',
            'Role',
            'Supervisor',
            'In Time',
            'In Location',
            'Out Time',
            'Out Location',
        ];


        $collection = match ( 'survey' ) {
            'survey' => SurveyReportResource::collection( $listData ),
        };

        $collectionData = $collection->toArray( $fields );

        return $this->csvExport($collectionData, $fields, '/uploads/reports');
    }


}
