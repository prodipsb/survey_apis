<?php

namespace App\Http\Controllers;

use App\Http\Resources\SurveyResource;
use App\Http\Traits\GlobalTraits;
use App\Models\SurveyArchive;
use Illuminate\Http\Request;

class ArchivedSurveyController extends Controller
{
    use GlobalTraits;

    protected $limit = 20;  

    public function archivedSurveys(Request $request){


        try {
            $listData = SurveyArchive::query();

            if ($request->has('binSearch') && $request->binSearch) {
                $listData = $listData->where('bin_number', 'like', '%'.$request->binSearch.'%');
            }


            $listData = $listData->where('survey_type', 'Archive');

            // $listData = $listData->paginate($this->limit, ['*'], 'page', $request->page);
            $listData = $listData->paginate($this->limit);
            // $listData = $listData->orderBy('id', 'desc')->get();

            return $this->throwMessage(200, 'success', 'All the list of Archived Surveys ', $listData);

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }

    }
}
