<?php

namespace App\Http\Controllers;

use App\Http\Traits\GlobalTraits;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\Role as RoleModel;

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

            if(auth()->user()->id){
                $totalTodaySubmittedSurveyCount = $listData1Clone->where('user_id', auth()->user()->id)->where('date', $today)->pluck('totalSurvey')->first();
                $totalMonthlySubmittedSurveyCount = $listData->where('user_id', auth()->user()->id)->whereBetween('created_at', [$startMonth, $endMonth])->pluck('totalSurvey')->first();
                $totalSubmittedSurveyCount = $listData->where('user_id', auth()->user()->id)->pluck('totalSurvey')->first();
            }else{
                $totalTodaySubmittedSurveyCount = $listData1Clone->where('date', $today)->pluck('totalSurvey')->first();
                $totalMonthlySubmittedSurveyCount = $listData->whereBetween('created_at', [$startMonth, $endMonth])->pluck('totalSurvey')->first();
                $totalSubmittedSurveyCount = $listData->pluck('totalSurvey')->first();
            }

           

            if(Auth::user()->user_type == "admin"){
                $userStats = RoleModel::withCount('user')->get();

            }else{
                $userStats = [];

            }

          

            // return $this->throwMessage(200, 'success', 'dashboard', $rolesWithUserCount);
            

            //  $roles = Role::whereNot('id', 1)->get();

            //  foreach($roles as $role){
            //     $count = User::where('role_id', $role->id)->count();
            //     $userData = [
            //         'name' => $role->name, 'count' => $count
            //     ];

            //     $user[] = $userData;
            //  }

           
            $data = [
               'userStats' => $userStats,
               'stats' => [
                    [
                    'name' => 'Today Surveys',
                    'count' => $totalTodaySubmittedSurveyCount
                    ],
                    [
                        'name' => 'Monthly Surveys',
                        'count' => $totalMonthlySubmittedSurveyCount
                    ],
                    [
                        'name' => 'Total Surveys',
                        'count' => $totalSubmittedSurveyCount
                    ]
                ],
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
