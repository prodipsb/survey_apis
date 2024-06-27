<?php

namespace Database\Seeders;

use App\Models\SurveyArchive;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class SurveyArchiveDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = storage_path('app/public/assets/survey_archive/survey-archive-data-info.xlsx'); // Path to your Excel file

        // Read data from Excel file
        $data = Excel::toCollection(null, $file);
        dd('excel', $data);

        // Insert data into the database
        foreach ($data[0] as $row) {
            SurveyArchive::create([
                'column1' => $row[0], // Assuming column1 is the first column in your Excel file
                'column2' => $row[1], // Assuming column2 is the second column in your Excel file
                // Add other columns as needed
            ]);
        }
    }
}
