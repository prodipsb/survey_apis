<?php

namespace App\Http\Controllers;

use App\Http\Traits\GlobalTraits;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    use GlobalTraits;
    protected $model = 'Survey';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $listData = $this->getModel($this->model)::select(DB::raw('count(user_id) as totalSurvey'))->process();
            $listData1Clone = $listData->clone($listData);

            $today =  Carbon::today(); 
            $startMonth = Carbon::today()->startOfMonth()->format('Y-m-d H:i:s');
            $endMonth = Carbon::today()->endOfMonth()->format('Y-m-d H:i:s');

            $totalTodaySubmittedSurveyCount = $listData1Clone->where('date', $today)->pluck('totalSurvey')->first();
            $totalMonthlySubmittedSurveyCount = $listData->whereBetween('created_at', [$startMonth, $endMonth])->pluck('totalSurvey')->first();

            // $user = User::query();

           
            $data = [
                'totalActivationOfficerCount' => 0,
                'totalSupervisorCount' => 0,
                'totalTeritoryOfficerCount' => 0,
                'totalAreaManagerCount' => 0,
                'totalTodaySubmittedSurveyCount' => $totalTodaySubmittedSurveyCount,
                'totalMonthlySubmittedSurveyCount' => $totalMonthlySubmittedSurveyCount
            ];
            
            
            return $this->throwMessage(200, 'success', 'dashboard', $data);


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
