<?php

namespace App\Http\Controllers;

use App\Http\Traits\GlobalTraits;
use App\Models\Attendance;
use App\Services\GeocodingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class AttendanceController extends Controller
{

    use GlobalTraits;


    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $auth = Auth::user();
        if($auth->user_type == 'admin'){
            $attendances = Attendance::latest()->get();
        }else{
            $attendances = Attendance::where([
                'user_id' => $auth->id,
            ])->latest()->get();
    
        }
        

        return $this->throwMessage(200, 'success', 'User Attendances!', $attendances);
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
    public function storeAttendance(Request $request)
    {
        $auth = Auth::user();

        $userAtt = $this->getUserCurrentAtt();

        $geoLocation = $this->geocodingService->getLocationName($request->latitude, $request->longitude);

        // $geoLocation = $this->getGeoLocation($request->latitude, $request->longitude);

        if($userAtt && $request->latitude){

            $geo = [
                'out_latitude' => $request->latitude,
                'out_longitude' => $request->longitude,
                'out_location' => $geoLocation['display_name'],
                'out_json_location' => json_encode($geoLocation)
            ];

            $userAtt->update($geo);

            return $this->throwMessage(200, 'success', 'User Attendance Updated!', $userAtt);
        }

        
        
        $att = [
            'user_id' => $auth->id,
            'employee_id' => $auth->employee_id,
            'role_id' => $auth->role_id,
            'supervisor_user_id' => $auth->supervisor_user_id,
            'date' => now(),
            'in_latitude' => $request->latitude,
            'in_longitude' => $request->longitude,
            'in_location' => $geoLocation['display_name'],
            'in_json_location' => json_encode($geoLocation),

        ];

        $userAttendance = Attendance::create($att);

        return $this->throwMessage(200, 'success', 'User Attendance Stored!', $userAttendance);

    }


    public function getGeoLocation($latitude, $longitude){

        // Make the API call
        $response = Http::get("https://nominatim.openstreetmap.org/reverse?format=json&lat=$latitude&lon=$longitude");

        // Get the JSON response body
        return $response->json();

    }


    public function getUserCurrentAtt()
    {
        $auth = Auth::user();
        $attendance = Attendance::where([
            'user_id' => $auth->id,
            'date' => Carbon::now()->format('Y-m-d')
        ])->first();

        return $attendance;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\Response
     */
    public function getUserCurrentAttendance()
    {
        $auth = Auth::user();
        $attendance = Attendance::where([
            'user_id' => $auth->id,
            'date' => Carbon::now()->format('Y-m-d')
        ])->first();

        return $this->throwMessage(200, 'success', 'User Attendance', $attendance);
    }


}
