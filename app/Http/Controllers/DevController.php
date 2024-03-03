<?php

namespace App\Http\Controllers;

use App\Http\Traits\GlobalTraits;
use App\Models\Device;
use App\Models\Survey;
use App\Models\SurveyArchive;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DevController extends Controller
{
    use GlobalTraits;

    public function index(){

        $userDevices = Device::with('userInfo')->get();

        foreach($userDevices as $userDevice) {
            // Assuming that the role_id is in the users table
            $userDevice->role_id = $userDevice?->userInfo?->role_id;
            $userDevice->save();
        }

        return $this->throwMessage(200, 'success', 'User Device Role Updated');


    }

    public function excelUpload(){
       // dd('Hello');

        $file = storage_path('app/public/survey_archive/survey-archive-data-info.xlsx'); // Path to your Excel file
      //  dd('$file', $file);

        // Read data from Excel file
        // $data = Excel::toCollection(null, $file);
       // $data = Excel::toCollection(null, $file)->skip(1); // Skip the first row
        $data = Excel::toCollection(null, $file);
        $data = $data[0]->skip(1);
       // dd('excel', $data);

        // Insert data into the database
        foreach ($data as $row) {
          
            SurveyArchive::create([
                'bin_number' => $row[1], 
                'bin_holder_name' => $row[2], 
                'bin_holder_address' => $row[3], 
                'division' => $row[4], 
                'circle' => $row[5], 
                'commissionerate' => $row[6], 
                'zone' => $row[7], 
                'email' => $row[8], 
                'mobile' => $row[9], 
            ]);
        }


        return $this->throwMessage(200, 'success', 'Survey Archive Data Stored Successfully!');

    }


    public function checkBinNumber(Request $request){
        $binNumber = $request->bin_number;

        $binExist = SurveyArchive::where('bin_number', $binNumber)->first();

        if($binExist){
            return $this->throwMessage(200, 'success', 'Bin Number Found In Survey Archive!', $binExist);
        }else{
            $binCheck = Survey::where('binNumber', $binNumber)->first();

            if($binCheck){
                return $this->throwMessage(200, 'success', 'Bin Number Found In Survey!', $binCheck);
            }
        }

        return $this->throwMessage(204, 'error', 'Unique Bin Number!');


    }



}
