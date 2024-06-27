<?php

namespace App\Http\Controllers;

use App\Http\Traits\GlobalTraits;
use App\Models\BINInformation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BINInformationController extends Controller
{
    use GlobalTraits;

    public function index()
    {
        //
    }


    public function importExcelForDeviceInformation()
    {
        // dd('aaa');
        $file = storage_path('app/public/bin_information/BINInformation.xlsx');
        $data = Excel::toCollection(null, $file);
        $data = $data[0]->skip(1);

        // Convert $data to Laravel Collection
        $data = collect($data);

        // Chunk size for processing
        $chunkSize = 500; // Adjust the chunk size as per your requirements

        // Process data in chunks
        $data->chunk($chunkSize)->each(function ($chunk) {
            $insertData = [];

            foreach ($chunk as $row) {
                $insertData[] = [
                    'serialNumber' => $row[1],
                    'binNumber' => $row[2],
                    'device' => $row[3],
                    'outletName' => $row[4],
                    'outletAddress' => $row[5],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                //dd($insertData);
            }

            // Insert data into the database
            BINInformation::insert($insertData);
        });

        return $this->throwMessage(200, 'success', 'BIN Device Data Stored Successfully!');
    }

    public function getBinNumberDetails(Request $request)
    {
        try {

            $BINDetails = BINInformation::where('binNumber', $request->binNumber)->get();
            return $this->throwMessage(200, 'success', 'BIN Number Details', $BINDetails);
        } catch (\Exception $e) {
            return $this->throwMessage(404, 'error', 'BIN Number not found');
        }
    }
}
