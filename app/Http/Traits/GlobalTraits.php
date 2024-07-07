<?php

namespace App\Http\Traits;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\FuncCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Notification;
use App\Models\ServiceIssue;
use App\Models\Survey;
use App\Models\SurveyArchive;
use App\Models\SurveyItem;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\TryCatch;
use Intervention\Image\Facades\Image;

trait GlobalTraits
{
    public function getAuthUser()
    {
        $user = Auth::user();
        return $user;
    }

    public function getAuthID()
    {
        $authID = Auth::id();
        return $authID;
    }

    public function getAuthRoleId()
    {
        $roleID = Auth::user()->role_id;
        return $roleID;
    }

    

    public function getAuthName()
    {
        $authName = Auth::user()->name;
        return $authName;
    }

    public function getAuthUserType()
    {
        return auth::user()->user_type;
    }

    public function getUserByEmail($email)
    {
        $user = $this->getModel('User')->where('email', $email)->first();
        return $user;
    }

    public function getUserById($id)
    {
        $user = $this->getModel('User')->where('id', $id)->first();
        return $user;
    }

    public function getCourseTitle($id){
        $course = $this->getModel('Course')->where('id', $id)->first();
        return $course->title;
    }

    public function isSuperAdmin($userType)
    {
        if ($userType == 'admin') {
            return true;
        } else {
            return false;
        }
    }


    public function isAdmin($id){
        $admin = $this->getModel('User')->where(['id' => $id, 'userType' => 'admin'])->first();
        if ($admin) {
            return true;
        } else {
            return false;
        }
    }


    public function getAuthUserPermissions($search){

         $permissions = [];
         $user = Auth::user();

         
         foreach ($user->roles as $role) {
            $permissions = array_merge($permissions, $role->permissions->pluck('name')->toArray());
        }


        // Custom callback function to filter elements
        $filteredPermissions = array_filter($permissions, function ($name) use ($search) {
            // Case-insensitive partial match
            return stripos($name, $search) !== false;
        });

        return array_values($filteredPermissions);



    }



    

    public function throwMessage($code, $status, $message, $data = false)
    {
        if ($data) {
            return response()->json([
                'code' => $code,
                'status' => $status,
                'message' => $message,
                'data' => $data,
                //   'count' => $data->count()
            ], $code);
        }
        return response()->json([
            'code' => $code,
            'status' => $status,
            'message' => $message
        ], $code);
    }

    public function getModel($model)
    {
        $ModelName = 'App\\Models\\' . $model;
        $model = new $ModelName;
        return $model;
    }

    public function getlist($model, $type = null, $with = [])
    {
        $model = $this->getModel($model);
        $data = $model::where('status', 1)->latest()->with($with)->get();
        return $data;
    }

    public function storeData(Request $request, $model, $fileUpload=false, $fileInputNames = ['image'], $path = 'uploads', $exceptFieldsArray = [])
    {
        $model = $this->getModel($model);
        $storeInput = $request->except($exceptFieldsArray);
        $insertedData = $model::create($storeInput);
        if ($fileUpload) {
            $this->checkFileDirectory($path);
            foreach ($fileInputNames as $fileInputName) {
                if (!empty($fileInputName) && $request->has($fileInputName)) {
                    $filename = $this->uploadFile($request, $fileInputName, $path);
                    $filePath = $filename ? '/'. $path . '/' . $filename : '';
                    $insertedData->$fileInputName = $filePath;
                    $insertedData->save();
                }
            }
        }
        

        return $insertedData;
    }


    public function uploadFile($request, $inputName = 'image', $path = 'uploads')
    {
        $filename = '';
        if ($request->hasFile($inputName)) {

            $file = $request->file($inputName);
            $filename = time().'_'. rand(10, 100). '.' . $file->getClientOriginalExtension();
            
            if($inputName == 'favicon'){

                $img = Image::make($file->path());
                $img->resize(32, 32)->save($path.'/'.$filename);

            }else{
                $file->move($path, $filename);
            }
    
           
            
        }
        return $filename;
    }


    public function storeServiceIssues($service_id, $issues)
    {
        foreach ($issues as $issue) {
            ServiceIssue::create([
                'service_id' => $service_id,
                'issue_id' => $issue
            ]);
        }
        return true;
    }


    public function getEditTableData($id, $model, $with = [])
    {
        $model = $this->getModel($model);
        if ($with) {
            $data = $model::where('id', $id)->with($with)->first();
        } else {
            $data = $model::findOrFail($id);
        }
        return $data;
    }

    public function removeExistingFile($filePath)
    {
        File::exists(public_path($filePath));
        File::delete(public_path($filePath));
        return true;
    }

    public function updateData(Request $request, $id, $model, $exceptFieldsArray, $fileUpload=false, $fileInputNames = ['image'], $path = 'uploads')
    {
        $model = $this->getModel($model);
        $updatedInput = $request->except($exceptFieldsArray);
        $data = $model::where('id', $id)->update($updatedInput);
        $data = $model::find($id);
        if ($fileUpload) {
            foreach ($fileInputNames as $fileInputName) {
                if ($request->hasFile($fileInputName) && $request->has($fileInputName)) {

                    $this->removeExistingFile($data->$fileInputName);
                   
                    $filename = $this->uploadFile($request, $fileInputName, $path);
                    $filePath = '/'.$path . '/' . $filename;
                    $data->$fileInputName = $filePath;
                    $data->save();
                }
            }
        }
        return $data;
    }

    public function storeMultipleFileData(Request $request, $model, $fileUpload=false, $fileInputName = 'image', $path = 'uploads', $exceptFieldsArray = ['image'])
    {
        $model = $this->getModel($model);
        if ($fileUpload) {
            if ($request->hasfile($fileInputName)) {
                $files = $request->file($fileInputName);
                foreach ($files as $file) {
                    // $storeInput = $request->except($exceptFieldsArray);
                    $storeInput = $request->only('survey_id');
                
                    try{
                    
                        $insertedData = SurveyItem::create($storeInput);
                        $filename = time().'_'. rand(10, 100). '.' . $file->getClientOriginalExtension();
                        $filePath = $path . '/' . $filename;
                        $destinationPath = public_path($path);
                        $file->move($destinationPath, $filename);
                        $insertedData->url = $filePath;
                        $insertedData->save();

                    } catch (\Exception $e) {
                        return $this->throwMessage(413, 'error', $e->getMessage());
                    }
                }
            }
        }
        return $insertedData;
    }

    public function updateMultipleFileData(Request $request, $id, $model, $exceptFieldsArray, $fileUpload=false, $fileInputName = 'image', $path = 'uploads')
    {
        $model = $this->getModel($model);
        $updatedInput = $request->except($exceptFieldsArray);
        $data = $model::where('id', $id)->update($updatedInput);
        $data = $model::find($id);
        if ($fileUpload) {
            if ($request->hasfile($fileInputName)) {
                $files = $request->file($fileInputName);
                foreach ($files as $file) {
                    $filename = time().'_'. rand(10, 100). '.' . $file->getClientOriginalExtension();
                    $filePath = $path . '/' . $filename;
                    $destinationPath = public_path($path);
                    $file->move($destinationPath, $filename);
                    $data->$fileInputName = $filePath;
                    $data->save();
                }
            }
        }
        return $data;
    }





   


    public function availabiltyCheck($id, $model)
    {
        $model = $this->getModel($model);
        $data = $model::find($id);
        if (empty($data)) {
            return false;
        } else {
            return true;
        }
    }

    public function availabiltyUniqueTitle($title, $model)
    {
        $model = $this->getModel($model);
        $data = $model::where('title', $title)->first();
        if (empty($data)) {
            return false;
        } else {
            return true;
        }
    }

    public function availabiltyUniqueName($name, $model)
    {
        $model = $this->getModel($model);
        $data = $model::where('name', $name)->first();
        if (empty($data)) {
            return false;
        } else {
            return true;
        }
    }

    public function checkInactiveAvailabilty($id, $model)
    {
        $data='';
        $model = $this->getModel('Course');
        if ($model == 'CourseCategory') {
            $data = $model::where('category_id', $id)->first();
        } elseif ($model == 'Language') {
            $data = $model::where('lang_id', $id)->first();
        } else {
            $data = $model::where('lang_id', $id)->first();
        }
        if (empty($data)) {
            return false;
        } else {
            return true;
        }
    }

    public function activeSingleData($model)
    {
        $model = $this->getModel($model);
        $data = $model::where('status', 1)->first();
        return $data;
    }

    public function checkFileDirectory($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
            return true;
        }
        return false;
    }

    public function mdistroy($id, $model)
    {
        $model = $this->getModel($model);
        $data = $model::findOrFail($id);
        if ($data->delete()) {
            return true;
        } else {
            return false;
        }
    }


    // public function csvExport($data = [], $heading = [], $path = '') {
    //     $fileName = 'export_' . time() . '.csv';
    
    //     $headers = [
    //         'Content-Encoding' => 'UTF-8',
    //         'Content-type' => 'text/csv; charset=utf-8',
    //         'Content-Disposition' => 'attachment; filename=' . $fileName,
    //         'Pragma' => 'no-cache',
    //         'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
    //         'Content-Transfer-Encoding' => 'binary',
    //         'Expires' => '0',
    //     ];
    
    //     $columns = $heading;
    
    //     $file = fopen('php://temp', 'w+'); // Use php://temp stream for temporary storage
    
    //     fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM
    
    //     fputcsv($file, $columns);
    
    //     foreach ($data as $item) {
    //         fputcsv($file, $item);
    //     }
    
    //     rewind($file); // Rewind the stream
    
    //     $csvData = stream_get_contents($file); // Get the CSV data
    
    //     fclose($file);
    
    //     return response($csvData)
    //         ->withHeaders($headers);
    // }


    
    


    // public function csvExport( $data = [], $heading = [], $path = '' ) {

    //     $fileName = 'export_'.time().'.csv';

    //     $headers = array(
    //         "Content-Encoding"          => "UTF-8",
    //         "Content-type"              => "text/csv; charset=utf-8",
    //         "Content-Disposition"       => "attachment; filename=" . $fileName,
    //         "Pragma"                    => "no-cache",
    //         "Cache-Control"             => "must-revalidate, post-check=0, pre-check=0",
    //         "Content-Transfer-Encoding" => "binary",
    //         "Expires"                   => "0",
    //     );

    //     $columns = $heading;




       








    //     $filePath = public_path( $path . '/'.$fileName );
    //     // $file     = fopen( $filePath, 'w' );
    //     $file = fopen('php://output', 'w');

    //     fputs( $file, "\xEF\xBB\xBF" ); // UTF-8 BOM !!!!!

    //     fprintf( $file, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );

    //     fputcsv( $file, $columns );

    //     foreach ( $data as $item ) {
    //         fputcsv( $file, $item );
    //     }

    //     //  dd('ddd', $file);

    //     fclose( $file );



    //     // return response()->stream($file, 200, $headers);
       



    //     //return Response::download($filePath, $fileName);

    //     // send_slack_message($this->report_type . ' exported to ' . appUrl("/{$path}"), 'reports');

    //     // return [ 'success' => 'Reported exported', 'data' => [ 'file' => $path, 'url' => appUrl("/{$path}") ] ];
    //    //  return response()->streamDownload( $file, $fileName, $headers )->send();
    //     // return response()->download( $filePath, $fileName, $headers )->send();

    // }



    // public function csvExport($data =[], $heading = [])
    // {
    //     $fileName = 'export_'.time().'.csv';
        
    //     $headers = array(
    //         "Content-type" => "text/csv",
    //         "Content-Disposition" => "attachment; filename=".$fileName,
    //         "Pragma" => "no-cache",
    //         "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
    //         "Expires" => "0"
    //     );
        
    //     $columns = $heading;
    //   //  dd('$columns', $columns);

    //     $callback = function () use ($data, $columns) {
    //         $file = fopen('php://output', 'w');
    //         fputcsv($file, $columns);

    //         foreach ($data as $item) {
    //             fputcsv($file, $item);
    //         }
    //         fclose($file);
    //     };
    //     return response()->stream($callback, 200, $headers);
    // }


    public function csvExport($data = [], $heading = [])
{

    // dd($data);

    $fileName = 'export_' . time() . '.csv';

    $headers = array(
        "Content-type" => "text/csv",
        "Content-Disposition" => "attachment; filename=" . $fileName,
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    );

    $columns = $heading;

    $callback = function () use ($data, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($data as $item) {
           // dd($item);
            // Check if the number of elements in $item matches the number of elements in $columns
            if (count($item) !== count($columns)) {
                // If not, fill in missing values with empty strings
                $item = array_pad($item, count($columns), '');
            }
            fputcsv($file, $item);
        }
        fclose($file);
    };
    return response()->stream($callback, 200, $headers);
}




    // public function csvExport($data =[], $heading = [])
    // {
    //     $fileName = 'export_'.time().'.csv';
        
    //     $headers = array(
    //         "Content-type" => "text/csv",
    //         "Content-Disposition" => "attachment; filename=".$fileName,
    //         "Pragma" => "no-cache",
    //         "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
    //         "Expires" => "0"
    //     );
        
    //     $columns = $heading;

    //     $callback = function () use ($data, $columns) {
    //         $file = fopen('php://output', 'w');
    //         fputcsv($file, $columns);

    //         foreach ($data as $item) {
    //             fputcsv($file, $item);
    //         }
    //         fclose($file);
    //     };
    //     return response()->stream($callback, 200, $headers);
    // }


    // public function applyFilter($request, $userQuery)
    // {
    //     if ($request->has('search') && $request->get('search') != '') {
    //         $s_text = $request->get('search');
    //         $userQuery->where('title', 'LIKE', "%{$s_text}%")->orWhere('short_desc', 'like', '%'.$request->search.'%');
    //     }

    //     if ($request->has('category_id') && $request->get('category_id') != '') {
    //         $category_ids = $this->strToArray($request->get('category_id'));
    //         $userQuery->whereIn('category_id', $category_ids);
    //     }

    //     if ($request->has('level_id') && $request->get('level_id') != '') {
    //         $level_ids = $this->strToArray($request->get('level_id'));
    //         $userQuery->whereIn('level_id', $level_ids);
    //     }

    //     if ($request->has('lang_id') && $request->get('lang_id') != '') {
    //         $lang_ids = $this->strToArray($request->get('lang_id'));
    //         $userQuery->whereIn('lang_id', $lang_ids);
    //     }

    //     if ($request->has('tags') && $request->get('tags') != '' && $request->has('mTag')) {
    //         $tags = $this->strToArray($request->get('tags'));
    //         $courseIds = CourseTag::whereIn('tag_id', $tags)->groupBy('course_id')->pluck('course_id')->all();
    //         $enrolledUserModel = $this->getModel('CourseEnrolledUser');
    //         $userCourseIds = $enrolledUserModel::whereIn('course_id', $courseIds)->where('user_id', $this->getAuthID())->pluck('course_id')->all();
    //        // $courses = Course::whereIn('id', $userCourseIds)->with(['enrollUserInfo', 'course_quizzes', 'myFeetback']);
    //         $courses = Course::process()->whereIn('id', $userCourseIds)->with(['enrollUserInfo', 'course_quizzes', 'myFeetback']);
    //         if ($request->load) {
    //             $courses = $courses->take($request->load * 4);
    //         }
    //         return $courses;
    //         // $courses = $courses->paginate($this->limit);
    //         // return CourseResource::collection($courses);
    //     }

    //     if ($request->has('tags') && $request->get('tags') != '') {
    //         $tags = $this->strToArray($request->get('tags'));
    //         $courseIds = CourseTag::whereIn('tag_id', $tags)->groupBy('course_id')->pluck('course_id')->all();
    //      //   $listData = Course::process()->where('status', 'published');
    //      //   $courses = Course::whereIn('id', $courseIds)->with('enrollUserInfo');
    //         $courses = Course::process()->whereIn('id', $courseIds)->with('enrollUserInfo');
    //         if ($request->load) {
    //             $courses = $courses->take($request->load * 4);
    //         }
    //         return $courses;
    //         // $courses = $courses->paginate($this->limit);
    //         // return CourseResource::collection($courses);
    //     }

    //     if ($request->has('sort') && $request->get('sort') != '') {
    //         $res = $this->sortDefine($request->sort);
    //         $userQuery->orderBy($res['column'], $res['direction']);
    //         //  return $userQuery->get();
    //     }

    //     if ($request->has('feedbacksearch') && $request->get('feedbacksearch') != '') {
    //         $s_text = $request->get('feedbacksearch');
    //         $userQuery->where('feedback', 'LIKE', "%{$s_text}%");
    //     }


    //     if ($request->load) {
    //         $userQuery->take($request->load * 4);
    //     }

    //     return $userQuery;
    // }

    public function strToArray($str)
    {
        return explode(',', $str);
    }

    public function sortDefine($type)
    {
        if ($type == "latest") {
            $column = 'created_at';
            $direction = 'desc';
        } elseif ($type == "older") {
            $column = 'created_at';
            $direction = 'asc';
        } elseif ($type == "updated") {
            $column = 'updated_at';
            $direction = 'desc';
        } elseif ($type == "a2z") {
            $column = 'title';
            $direction = 'asc';
        } elseif ($type == "z2a") {
            $column = 'title';
            $direction = 'desc';
        } else {
            $column = 'created_at';
            $direction = 'desc';
        }
        $data = [
            'column' => $column,
            'direction' => $direction
        ];
        return $data;
    }


    public function checkBinNumber(Request $request){
        $binNumber = $request->binNumber;

        $binExist = SurveyArchive::where('bin_number', $binNumber)->first();
        // dd('$binExist', $binExist);

        if($binExist){
            return true;
            // return $this->throwMessage(200, 'success', 'Bin Number Found In Survey Archive!', $binExist);
        }else{
            $binCheck = Survey::where('binNumber', $binNumber)->first();
            if($binCheck){
                return true;
                // return $this->throwMessage(200, 'success', 'Bin Number Found In Survey!', $binCheck);
            }
        }

        return false;

        // return $this->throwMessage(204, 'error', 'Unique Bin Number!');


    }


    public function sendPushNotification($model, $storeInput) {

        $model = $this->getModel($model);
        $insertedData = $model::create($storeInput);

        return $insertedData;

    }


    
}
