<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Traits\GlobalTraits;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends Controller
{

    use GlobalTraits;

    protected $model = 'User';
    protected $uploadDir = 'uploads/avatar';
    protected $limit = 10;  

    public function index(Request $request)
    {
        try {

            $listData = new User();
           
            $listData = $listData->with(['role', 'supervisor', 'supervisorRole']);
            
            if ($request->has('start_date') && $request->has('end_date')) {
                $listData = $listData->whereBetween('date_of_joining', [$request->start_date, $request->end_date]);
            } 

            if($request->has('search')) {
                $listData = $listData->when(request('search'), function ($query, $search) {
                   $query->whereFullText([
                    'name',
                    'phone',
                    'email'
                   ], $search);
               });
           }

            if ($request->has('export') && $request->get('export') == true) {
                $listData = UserResource::collection($listData->get());
              //  dd($listData);
                $fields = [
                    'employee_id',
                    'name',
                    'email',
                    'phone',
                    'role',
                    'supervisor',
                    'supervisor_name',
                    'gender',
                    'date_of_joining',
                    'country',
                    'zone',
                    'commissionerate',
                    'division',
                    'circle',
                    'address',
                    'status'
                ];
              //  $listData = $listData->toArray($fields);


                 // Convert each resource to array
                $listDataArray = $listData->map(function ($resource) {
                    return $resource->toArray(request());
                });

                // Extract only the specified fields
                $listDataExport = [];
                foreach ($listDataArray as $item) {
                   
                    $rowData = [];
                    foreach ($fields as $field) {
                        
                        $rowData[] = $item[$field] ? $item[$field] : "null"; // Use empty string if the field is not present
                    }
                    $listDataExport[] = $rowData;
                }

                return $this->csvExport($listDataExport, $fields);
            }
            // if($request->search){
            //     $listData = $listData->where('name', 'like', '%'.$request->search.'%');
            // }

            $listData = $listData->orderBy('created_at', 'desc');

           // $listData = $this->applyFilter($request, $listData);
            $listData = $listData->paginate($this->limit);
            //return CourseResource::collection($listData);

            return $this->throwMessage(200, 'success', 'List of Users ', $listData);

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
        

    }


    public function getUsers()
    {
        try {

            $listData = new User();
            $listData = $listData->select('id', 'name');
            $listData = $listData->orderBy('created_at', 'desc');
            $listData = $listData->get();

            return $this->throwMessage(200, 'success', 'All the list of Courses ', $listData);

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
        

    }



    public function createUser(Request $request)
    {

        $user = $request->user();

        //Check if this is super admin or not
       

        // $this->validate($request, [
        //     'name' => 'required',
        //     'role_id' => 'required',
        //     'email' => 'required | email | unique:users',
        //     'password' => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
        // ]);

        $inputs = $request->all();


        // Create new user
        if(!empty($request->id)){


            if (!$this->isSuperAdmin($user->user_type)) {
                return $this->throwMessage(413, 'error', 'Permission not granted, Only Admin has the access to register new user');
            }

            $rules = [
                'name' => 'required',
                'role_id' => 'required',
            ];


            $user = User::findOrFail($request->id);
            $message = 'User Data Updated Successfully';


        }else{

            $rules = [
                'name' => 'required',
                'email'    => 'required | email | unique:users',
                'role_id' => 'required',
                'password' => 'required|max:6',
            ];

            $user = new User();
            $user->email = $request->email;
            $message = 'User Created Successfully';

        }


        $validation = Validator::make( $inputs, $rules );
    
        if ( $validation->fails() ) {
            return $this->throwMessage(400, 'error', $validation->errors());
           // return $validation->errors(); 
        }

        $role = Role::findOrFail($request->role_id);


        try {

            if(!empty($request->id)){

               // return $this->throwMessage(200, 'success', ["user_role" => $user->role_id, "request_role" => $request->role_id]);
                if($user->role_id !== (int)$request->role_id){
                    $user->updateRoleAndPermissions($role);
                    // $user->removeRoles();
                    // // $user->roles()->detach();
                    // $user->assignRole($role);
                    // $user->syncPermissions($role->permissions);
                }


                if($request->password){
                    $hashPassword = Hash::make($request->password);
                    $request->merge(['password' => $hashPassword]);
                }

                $user = $this->updateData($request, $request->id, $this->model, $exceptFieldsArray = ['email', 'role', 'supervisor', 'supervisor_role', 'permissions', 'roles'], $fileUpload = true, $fileInputName = ['avatar'], $path = $this->uploadDir);

            }else{

                
                $hashPassword = Hash::make($request->password);
                $request->merge(['password' => $hashPassword]);
                $request->merge(['created_by' => $this->getAuthID()]);
                $request->merge(['country' => 'Bangladesh']);
                $request->merge(['user_type' => str::slug($role->name, '_')]);

                $user = $this->storeData($request, $this->model, $fileUpload = true, $fileInputName = ['avatar'], $path = $this->uploadDir);

                // assign user to role
                if($request->role_id){
                    $user->assignRole($role);
                    $user->syncPermissions($role->permissions);
                }

            }

            

            return $this->throwMessage(200, 'success', $message);


        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }




    public function getUser($id){

        try{
             $user = User::with(['role', 'supervisor', 'reportTo'])->findOrFail($id);
        
            return $this->throwMessage(200, 'success', 'user details', $user);

        } catch (\Exception $e) {
            return $this->throwMessage(404, 'error', 'User not Found');
        }
    }




    public function userProfile(){

        try {
            $permissions = [];
            $user = User::with('roles')->findOrFail(Auth::user()->id);
            foreach ($user->roles as $role) {
                $permissions = array_merge($permissions, $role->permissions->pluck('name')->toArray());
            }
            $user->permissions = $permissions;
            return $this->throwMessage(200, 'success', 'User Details', $user);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', 'User not found');
        }
       

    }


    public function userAvatarUpdate(Request $request){

        try {

            $user = User::findOrFail(Auth::user()->id);
            $filename = $this->uploadFile($request, $inputName='avatar', $path=$this->uploadDir);
            $filePath = '/'.$path . '/' . $filename;
            $user->avatar = $filePath;
            $user->save();

            return $this->throwMessage(200, 'success','user avatar', $user->avatar);

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', 'User not found');
        }
            

    }


    public function updateUserStatus(Request $request){

        try {

            $user = User::findOrFail($request->id);
            if($user->status == "Active"){
                $user->status = "Inactive";
            }else{
                $user->status = "Active";
            }

            $user->save();
            return $this->throwMessage(200, 'success','user status updated', $user);

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', 'User not found');
        }
            

    }



    public function passwordUpdate(Request $request){

        $authId = Auth::id();
        $inputs = $request->all();

        $rules = [
            'password' => 'required| min:6|confirmed',
            'password_confirmation' => 'required| min:6',
        ];

        $validation = Validator::make( $inputs, $rules );

        if ( $validation->fails() ) {
            return $this->throwMessage(400, 'error', $validation->errors());
        }



        try {

            $hashPassword = Hash::make($request->password);

            $request->merge(['password' => $hashPassword]);

            $this->updateData($request, $authId, $this->model, $exceptFieldsArray = ['password_confirmation'], $fileUpload = false);

            return $this->throwMessage(200, 'success','user password', 'User Password Updated Succesfully!');

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', 'User not found');
        }
        


    }

    public function importUser(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'upload_file' => 'required|mimes:csv,txt,xls,xlsx'
        ]);
    
        if ($validator->fails()) {
            return $this->throwMessage(400, 'error', $validator->errors()->first());
        }
    
        try {
            // Get the file path
            $inputFileName = $request->file('upload_file')->getRealPath();
    
            // Load the spreadsheet
            $spreadsheet = IOFactory::load($inputFileName);
    
            // Get the active sheet
            $sheet = $spreadsheet->getActiveSheet();
    
            // Get sheet data starting from the 2nd row
            $sheetData = $sheet->rangeToArray(
                'A2:' . $sheet->getHighestColumn() . $sheet->getHighestRow(),
                null,
                true,
                true,
                true
            );

           // return $this->throwMessage(200, 'success', "Import file successfully", $sheetData);
    
            // Upload user bulk data from CSV
            $this->uploadUserBulkDataFromCSV($sheetData);
    
            return $this->throwMessage(200, 'success', "Import file successfully");
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }


    public function uploadUserBulkDataFromCSV($data)
    {

        foreach ($data as $key => $row) {
                $userRole = Role::findByName($row['E']);
                $supervisorRole = Role::findByName($row['F']);
                $supervisor = User::where('name', $row['G'])->first();
                $user = User::create([
                    'employee_id' => $row['A'],
                    'name' => $row['B'],
                    'email' => $row['C'],
                    'phone' => $row['D'],
                    'role_id' => $userRole?->id,
                    'supervisor_id' => $supervisorRole?->id,
                    'supervisor_name' => $supervisor?->name,
                    'gender' => $row['H'],
                    'bio' => $row['I'],
                    'date_of_joining' => date('Y-m-d', strtotime($row['J'])),
                    'country' => $row['K'],
                    'zone' => $row['L'],
                    'commissionerate' => $row['M'],
                    'division' => $row['N'],
                    'circle' => $row['O'],
                    'address' => $row['P'],
                    'supervisor_user_id' => $supervisor?->id ?? "",
                    'user_type' => str::slug($row['E'], '_'),
                    'created_by' => 1,
                    'status' => "Active",
                    'password' => Hash::make(123456),
                ]);


                if ($userRole) {
                    // Retrieve the role object from the relationship
                    $role = $userRole->first();

                   
                
                    if ($role) {
                        // Assign the role to the user
                        $user->assignRole($role);
                
                        // Sync permissions associated with the role
                       // $user->syncPermissions($role->permissions()->pluck('name')->toArray());
                    } else {
                        return $this->throwMessage(404, 'error', 'Role not found');
                    }
                } else {
                    return $this->throwMessage(404, 'error', 'User role not specified');
                }


                

            //     // $role = $userRole->first(); // Retrieve the role object from the relationship
            //     // if ($role) {
            //     //     $user->assignRole($role);
            //     //     $user->syncPermissions($role->permissions);
            //     // } 


            // }

            //return $this->throwMessage(200, 'success', "Import file successfully");

            // if ($supervisor && ($user->id != $supervisor->id)) {
            //     Supervisor::create([
            //         'user_id' => $user->id,
            //         'supervisor_id' => $supervisor->id,
            //     ]);
            // }
            // foreach ($processes as  $process_id) {
            //     $process = AgentProcess::where('user_id', $user->id)->where('process_id', $process_id)->exists();
            //     if (!$process) {
            //         $user->agentProcess()->create(['process_id' => $process_id]);
            //     }
            // }
        // }
    }

    //return $this->throwMessage(200, 'success', "Import file successfully");
    return true;

}


    // public function demoFileExport(Request $request)
    // {
    //     if ($request->for == 'user_group_bulk') {
    //         $data = User::orderBy('id', 'desc')->where('status', 'active')->take(5);
    //         $listData = DemoFileResource::collection($data->get());
    //         $fields = ['username', 'name', 'phone', 'email', 'gender', 'user_type', 'user_group', 'supervisor_username', 'status'];
    //         $listData = $listData->toArray($fields);
    //         return $this->csvExport($listData, $fields);
    //     }
    // }


    public function deleteUser(Request $request)
    {
        try {
            User::findOrFail($request->id)?->delete();
            return $this->throwMessage(200, 'success', 'User deleted successfully');
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', 'User not found');
        }
    }









}
