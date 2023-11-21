<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Traits\GlobalTraits;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

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
           
            $listData = $listData->with(['role', 'supervisor', 'reportTo']);
            
            if ($request->has('start_date') && $request->has('end_date')) {
                $listData = $listData->whereBetween('date_of_joining', [$request->start_date, $request->end_date]);
            } 

            if($request->has('search')) {
                $listData = $listData->when(request('search'), function ($query, $search) {
                   $query->whereFullText([
                    'name',
                    'phone',
                    'email',
                    'user_type',
                    'location',
                    'bin_no'
                   ], $search);
               });
           }

            if ($request->has('export') && $request->get('export') == true) {
                $listData = UserResource::collection($listData->get());
                $fields = [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'user_type',
                    'gender',
                    'bio',
                    'date_of_joining',
                    'country',
                    'city',
                    'division',
                    'location',
                    'longitude',
                    'latitude',
                    'last_login',
                    'last_logout',
                    'status'

                ];
                $listData = $listData->toArray($fields);

                
             //   dd($listData);
                return $this->csvExport($listData, $fields);
            }
            // if($request->search){
            //     $listData = $listData->where('name', 'like', '%'.$request->search.'%');
            // }

            $listData = $listData->orderBy('created_at', 'desc');

           // $listData = $this->applyFilter($request, $listData);
            $listData = $listData->paginate($this->limit);
            //return CourseResource::collection($listData);

            return $this->throwMessage(200, 'success', 'All the list of Courses ', $listData);

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

            if (!$this->isSuperAdmin($user->email)) {
                return $this->throwMessage(413, 'error', 'Permission not granted, Only Super Admin has the access to register new user');
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


        try {

            if(!empty($request->id)){

                $user = $this->updateData($request, $request->id, $this->model, $exceptFieldsArray = ['password', 'email', 'role', 'supervisor', 'report_to'], $fileUpload = true, $fileInputName = ['avatar'], $path = $this->uploadDir);

            }else{

                if($request->role_id){
                    $role = Role::findOrFail($request->role_id);
                    $user->assignRole($role);
                }

                $hashPassword = Hash::make($request->password);

                $request->merge(['password' => $hashPassword]);
                $request->merge(['created_by' => $this->getAuthID()]);
                $request->merge(['country' => 'Bangladesh']);
                $request->merge(['user_type' => str::slug($role->name, '_')]);

                $user = $this->storeData($request, $this->model, $fileUpload = true, $fileInputName = ['avatar'], $path = $this->uploadDir);

                // assign user to role
                if($request->role_id){
                    $user->assignRole($role);
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
