<?php

namespace App\Http\Controllers;

use App\Http\Traits\GlobalTraits;
use App\Models\Survey;
use App\Models\SurveyArchive;
use App\Models\User;
use App\Notifications\SurveyNotification;
use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class SurveyController extends Controller
{

    use GlobalTraits;

    protected $model = 'Survey';
    protected $surveyItemModel = 'SurveyItem';
    protected $uploadDir = 'uploads/survey';
    protected $uploadSurveyItemDir = 'uploads/surveyItems';
    protected $limit = 10;  
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
    public function index(Request $request)
    {
        try {


            $listData = Survey::query();
           
            if ($request->has('start_date') && $request->has('end_date')) {
                $listData = $listData->whereBetween('created_at', [$request->start_date, $request->end_date]);
            } 

            $listData = $listData->orderBy('created_at', 'desc');

            $listData = $listData->paginate($this->limit);

            return $this->throwMessage(200, 'success', 'All the list of Surveys ', $listData);

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
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
    public function surveySubmission(Request $request)
    {

        // $binCheck = $this->checkBinNumber($request);
        // if($binCheck){
        //     return $this->throwMessage(422, 'error', 'Bin Number Already Exist!', $binCheck);
        // }



        $inputs = $request->all();


        if(!empty($request->id)){

            $rules = [
                'binHolderName' => 'required',
                'binNumber' => 'required',
                'shopName' => 'required',
            ];


            $survey = Survey::findOrFail($request->id);
            $message = 'Survey Data Updated Successfully';


        }else{

            $rules = [
                'binHolderName' => 'required',
                'binNumber' => 'required',
                'shopName' => 'required',
            ];

            $survey = new Survey();
            $message = 'Survey Data Submitted Successfully';

        }


        $validation = Validator::make( $inputs, $rules );
    
        if ( $validation->fails() ) { 

            return $this->throwMessage(422, 'error', $validation->errors()->first());

        }


        try {

            $request->merge(['user_id' => $this->getAuthID()]);
            $request->merge(['surveySubmittedUserName' => Auth::user()->name]);
            $request->merge(['surveySubmittedUserEmail' => Auth::user()->email]);
            $request->merge(['surveySubmittedUserPhone' => Auth::user()->phone]);
            $request->merge(['role_id' => $this->getAuthRoleId()]);
            $request->merge(['survey_type' => $request->survey_type ? 'Archive' : 'New']);

            if($request->has('latitude') && $request->has('longitude')){
                $geoLocation = $this->geocodingService->getLocationName($request->latitude, $request->longitude);
                if($geoLocation){
                    $request->merge(['tracked_location' => $geoLocation['display_name']]);

                }
            }

            $survey = $this->storeData($request, $this->model, $fileUpload = true, $fileInputName = ['shopPic', 'binCertificate'], $path = $this->uploadDir);
            
            // assign user to role
            if($request->itemList){
               $this->uploadSurveyItemList($request, $survey->id);
            }

            if($request->survey_type){
                SurveyArchive::where('bin_number', $request->binNumber)->update([
                    'survey_type' => 'New'
                ]);
            }

            return $this->throwMessage(200, 'success', $message);


        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }


    }


    public function checkBinNumberExistance(Request $request)
    {
        
            $binNumber = $request->binNumber;
            $binCheck = Survey::where('binNumber', $binNumber)->first();
    
            if ($binCheck) {
                return $this->throwMessage(200, 'success', 'Bin Number Found!', $binCheck);
            }
            
    
            return $this->throwMessage(204, 'error', 'Unique Bin Number!');
        
    }

    


    public function uploadSurveyItemList($request, $surveyId) { 

         $request->merge(['survey_id' => $surveyId]);
        try{

            $this->storeMultipleFileData($request, $this->surveyItemModel, $fileUpload=true, $fileInputName='itemList', $path = $this->uploadSurveyItemDir);
            return true;

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }


    public function getSurvey($id){

        try{
             $survey = Survey::with('surveyItems')->findOrFail($id);
        
            return $this->throwMessage(200, 'success', 'survey details', $survey);

        } catch (\Exception $e) {
            return $this->throwMessage(404, 'error', 'Survey not Found', $e->getMessage());
        }
    }


    public function surveyUpdate(Request $request)
    {

            $inputs = $request->all();

            $rules = [
                'id' => 'required',
                'binHolderName' => 'required',
                'binHolderMobile' => 'required',
                'shopName' => 'required',
            ];

            $message = 'Survey Data Updated Successfully';


        $validation = Validator::make( $inputs, $rules );
    
        if ( $validation->fails() ) { return $validation->errors(); }


        try {

            $surveyId = $request->id;
            if($request->has('latitude') && $request->has('longitude')){
                $request->merge(['tracked_location' => $this->geocodingService->getLocationName($request->latitude, $request->longitude)]);
            }
            $survey = $this->updateData($request, $surveyId, $this->model, $exceptFieldsArray = ['shopPic', 'binCertificate', 'itemList'], $fileUpload = true, $fileInputName = ['shopPic', 'binCertificate'], $path = $this->uploadDir);

            // ==== list of items files of survey
            if($request->itemList){
               $this->uploadSurveyItemList($request, $survey->id);
            }

            return $this->throwMessage(200, 'success', $message);


        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }


    }


    public function surveyDelete(Request $request){

        $surveyId = $request->id;   
        try {
            Survey::findOrFail($surveyId)->delete();
            return $this->throwMessage(200, 'success', 'Survey deleted successfully');
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', 'Survey not found');
        }

    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function getArchiveSurveys()
    {
        $archiveSurveys = SurveyArchive::where('survey_type', 'Archive')->get();
        $archiveSurveys = $archiveSurveys->paginate($this->limit);

        return $this->throwMessage(200, 'success', 'All Archive Survey Data ', $archiveSurveys);

    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Survey $survey)
    {
        //
    }
}
