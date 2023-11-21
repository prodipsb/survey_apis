<?php

namespace App\Http\Controllers;

use App\Http\Traits\GlobalTraits;
use GuzzleHttp\ClientTrait;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\AgentProcess;
use App\Models\Process;
use App\Models\Supervisor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AccessController extends Controller
{

    use GlobalTraits;
    protected $limit = 10; 
    public function throwMessage($code, $status, $message, $data = false)
    {
        if ($data) {
            return response()->json(
                [
                    'code' => $code,
                    'status' => $status,
                    'message' => $message,
                    'data' => $data
                ],
                $code
            );
        }
        return response()->json(
            [
                'code' => $code,
                'status' => $status,
                'message' => $message
            ],
            $code
        );
    }


    public function isSuperAdmin($email)
    {
        $model = new User();
        return $model->isSuperAdmin($email);
    }

    //Check if this is super admin or not
    public function superAdminCheck()
    {
        $user = auth()->user();
        if (!$this->isSuperAdmin($user->email)) {
            return false;
        } else {
            return true;
        }
    }

    public function roleCreate(Request $request)
    {

        $validator = Validator::make($request->all(), [ 'name' => 'required|unique:roles']);

        if ($validator->fails()) { return $validator->errors(); }

        $isSuper = $this->superAdminCheck();
        if (!$isSuper) {
            return $this->throwMessage(404, 'error', 'Permission denied, Only superadmin can access!');
        }

        //create
        try {
            
            Role::create(['name' => $request->name, 'guard_name' => 'api']);
            return $this->throwMessage(200, 'success', 'Role has Created Successfully!');
            
        } catch (\Throwable $th) {
            //throw $th;
            return $th->getMessage();
        }
       
    }

    public function checkIfExistRole($id)
    {
        $data = Role::find($id);
        return $data;
    }



    public function getRoles(Request $request)
    { 


        $permissions = $this->getAuthUserPermissions('role');
        
       
        try {
            $listData = Role::query();
            if($request->search){
                $listData = $listData->where('name', 'like', '%'.$request->search.'%');
            }


            if($request->pagination){
                $listData = $listData->paginate($this->limit);
            }else{
                $listData = $listData->get();
            }

            $listData = [
                'permissions' => $permissions,
                'data' => $listData
            ];
            


            return $this->throwMessage(200, 'success', 'All Relos', $listData);

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }


      //Assigning role
      public function setRole(Request $request)
      {

          $validator = Validator::make($request->all(), [ 'role_id' => 'required','users' => 'required' ]);

          if ($validator->fails()) { return $validator->errors(); }


          $isSuper = $this->superAdminCheck();
          if (!$isSuper) {
              return $this->throwMessage(404, 'error', 'Permission denied, Only superadmin can access!');
          }
          $users = $request->users;
          $role = Role::findOrFail($request->role_id);
          foreach ($users as $user) {
              $data = User::find($user);
              $data->assignRole($role);
          }
          return $this->throwMessage(200, 'success', 'Role assign successful');
      }



    public function removeRoleFromUser(Request $request)
    {
        if (!$this->superAdminCheck()) {
            return $this->throwMessage(404, 'error', 'Permission denied, Only superadmin can access!');
        }
        //delete all role for this user
        $model = new User();
        $user = $model->findOrFail($request->user_id);
        $user->roles()->detach();

        return $this->throwMessage(200, 'success', 'Successfully have removed all Role the User!');
    }


    public function deleteRole(Request $request)
    {
        $isSuper = $this->superAdminCheck();
        if (!$isSuper) {
            return $this->throwMessage(404, 'error', 'Permission denied, Only superadmin can access!');
        }
        $isExist = $this->checkIfExistRole($request->id);
        // dd($isExist);
        if (!$isExist) {
            return $this->throwMessage(404, 'error', 'Role is not exist!');
        }
        //create
        // $role = Role::findById($request->id);
        $role = Role::findOrFail($request->id);
        $role->delete();
        return $this->throwMessage(200, 'success', 'Role has deleted Successfully!');
    }




    // ========= Permisson Section =========

    public function permissionCreate(Request $request)
    {

        $isSuper = $this->superAdminCheck();
        if (!$isSuper) {
            return $this->throwMessage(404, 'error', 'Permission denied, Only superadmin can access!');
        }
        //create
        Permission::create(['name' => $request->name, 'guard_name' => 'api']);
        return $this->throwMessage(200, 'success', 'Permission has created Successfully!');
    }




    public function allPermission(Request $request)
    {

        $permissions = $this->getAuthUserPermissions('permission');

        try {
            $listData = Permission::query();
            if($request->search){
                $listData = $listData->where('name', 'like', '%'.$request->search.'%');
            }
            
           
           // $listData = $listData->orderBy('created_at', 'desc');
            
            if($request->pagination){
                $listData = $listData->paginate($this->limit);
            }else{
                $listData = $listData->get();
            }

            $listData = [
                'permissions' => $permissions,
                'data' => $listData
            ];
            

            return $this->throwMessage(200, 'success', 'All Permission', $listData);

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }



    }

    public function getPermissions(Request $request)
    {
        // return $request;
        if ($request->role_id) {
            $role_permissions = [];
            $role = Role::findById($request->role_id);
            $permission = $this->allPermission();
            foreach ($permission as $perm) {
                if ($role->hasPermissionTo($perm->name)) {
                    $perm->selected_permission = true;
                } else {
                    $perm->selected_permission = false;
                }
            }
        } else {
            $permission = $this->allPermission();
        }
        return $this->throwMessage(200, 'success', 'All the permissions list', $permission);
    }



    public function setPermission(Request $request)
    {

        $validator = Validator::make($request->all(), [ 'role_id' => 'required','permissions' => 'required' ]);

        if ($validator->fails()) { return $validator->errors(); }

        $isSuper = $this->superAdminCheck();
        if (!$isSuper) {
            return $this->throwMessage(404, 'error', 'Permission denied, Only superadmin can access!');
        }

      //  dd(Role::findById($request->role_id));

      try {


        $role = Role::findOrFail($request->role_id);
        

        foreach ($request->permissions as $val) {
            $permission = Permission::find($val);
            $role->givePermissionTo($permission->name);
        }

        return $this->throwMessage(200, 'success', 'Successfully set the permission!');


        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', 'Role not found');
        }
        
    }



    public function checkIfExistPermission($id)
    {
        $data = Permission::find($id);
        return $data;
    }


    public function removePermissionFromUser(Request $request)
    {
        if (!$this->superAdminCheck()) {
            return $this->throwMessage(404, 'error', 'Permission denied, Only superadmin can access!');
        }

        try {
        $permission = Permission::findOrFail($request->permission_id);
        $model = new User();
        $user = $model->findOrFail($request->user_id);
        $user->revokePermissionTo($permission->name);

        return $this->throwMessage(200, 'success', 'Successfully removed permission!');

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }



    public function deletePermission(Request $request)
    {
        $isSuper = $this->superAdminCheck();
        if (!$isSuper) {
            return $this->throwMessage(404, 'error', 'Permission denied, Only superadmin can access!');
        }
        $isExist = $this->checkIfExistPermission($request->id);
        if (!$isExist) {
            return $this->throwMessage(404, 'error', 'Permission is not exist!');
        }
        $permission = Permission::find($request->id);
        $permission->delete();
        return $this->throwMessage(200, 'success', 'Permission has deleted Successfully!');
    }

 

    public function userInformation()
    {
        $permissionData = [];
        $user = User::where('id', auth()->user()->id)->first();
        foreach (auth()->user()->roles as $role) {
            array_push($permissionData, $role->permissions->pluck('name'));
        }
        $user->permission = $permissionData;
        try {
            return $this->throwMessage(200, 'success', "User Information", $user);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    public function userInformationWithPermission(Request $request)
    {
       // dd($request->user_id);
        $permissionData = [];

        $user = User::findOrFail($request->user_id);

        foreach ($user->roles as $role) {
            array_push($permissionData, $role->permissions->pluck('name'));
        }
        $user->permission = $permissionData;
        try {
            return $this->throwMessage(200, 'success', "User Information", $user);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    public function userList()
    {
        try {
            $users = User::where('email', '!=', 'superadmin@admin.com')->get();
            return $this->throwMessage(200, 'success', "User Information", $users);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    public function agentList()
    {
        try{
            $users = User::where('email', '!=', 'superadmin@admin.com')->where('user_type', 'agent')->where('status', 'active')->get();
            return $this->throwMessage(200, 'success', "User Information", $users);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    public function supervisorList(Request $request)
    {
        $supervisors = User::select('id', 'name')->where('id', '!=', $request->user_id)->where('status', 'active')->get();
        return $this->throwMessage(200, 'success', "Supervisor List", $supervisors);
    }

    public function adminUserList(Request $request)
    {
        try {
            $data = User::where('email', '!=', 'superadmin@admin.com');
            // if ($request->text) {
            //     $data = $data->where('name', 'like', '%' . $request->text . '%');
            // }
            if ($request->text) {
                $data = $data->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->text . '%')
                        ->orWhere('email', 'like', '%' . $request->text . '%')
                        ->orWhere('phone', 'like', '%' . $request->text . '%')
                        ->orWhere(function ($p) use ($request) {
                            $p->whereHas('agentProcess', function ($r) use ($request) {
                                $r->whereHas('process', function ($s) use ($request) {
                                    $s->where('name', 'like', '%' . $request->text . '%');
                                });
                            });
                        });
                });
            }
            $data = $data->latest()->paginate(10);
            return $this->throwMessage(200, 'success', "User list of Information", $data);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    public function getRoleNameById(Request $request)
    {
        $isSuper = $this->superAdminCheck();
        if (!$isSuper) {
            return $this->throwMessage(404, 'error', 'Permission denied, Only superadmin can access!');
        }
        $isExist = $this->checkIfExistRole($request->id);
        if (!$isExist) {
            return $this->throwMessage(404, 'error', 'Role is not exist!');
        }
        try {
            $data = Role::find($request->id);
            return $this->throwMessage(200, 'success', "Role information", $data);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    public function getPermissionNameById(Request $request)
    {
        $isSuper = $this->superAdminCheck();
        if (!$isSuper) {
            return $this->throwMessage(404, 'error', 'Permission denied, Only superadmin can access!');
        }
        $isExist = $this->checkIfExistPermission($request->id);
        if (!$isExist) {
            return $this->throwMessage(404, 'error', 'Permission is not exist!');
        }
        try {
            $data = Permission::find($request->id)->pluck('name');
            return $this->throwMessage(200, 'success', "Permission information", $data);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    public function showUsersByRole(Request $request)
    {
        $isSuper = $this->superAdminCheck();
        if (!$isSuper) {
            return $this->throwMessage(404, 'error', 'Permission denied, Only superadmin can access!');
        }
        $isExist = $this->checkIfExistRole($request->id);
        if (!$isExist) {
            return $this->throwMessage(404, 'error', 'Role is not exist!');
        }


        try {

            $data = Role::where('id', $request->id)->pluck('name');
            $user = User::query();
            $users = $user->role($data[0]);

            if($request->search){
                $users = $users->select('id', 'name')->where('name', 'like', '%'.$request->search.'%');
            }
                        
            if($request->pagination){
                $users = $users->paginate($this->limit);
            }else{
                $users = $users->get();
            }


            // $users = User::role($data[0])->get();
            return $this->throwMessage(200, 'success', "Role information", $users);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }




    public function getExcludeRoleUsers(Request $request)
    {

      //  dd($request->role_id);
        $isSuper = $this->superAdminCheck();
        if (!$isSuper) {
            return $this->throwMessage(404, 'error', 'Permission denied, Only superadmin can access!');
        }
        $isExist = $this->checkIfExistRole($request->role_id);
        if (!$isExist) {
            return $this->throwMessage(404, 'error', 'Role is not exist!');
        }


       


        try {

        $data = Role::where('id', $request->role_id)->pluck('name');

        $users = User::select('id', 'name')->whereDoesntHave('roles', function ($query) use ($data) {
            $query->where('name', $data[0]);
        });

        if($request->search){
            $users = $users->select('id', 'name')->where('name', 'like', '%'.$request->search.'%');
        }
                    
        if($request->pagination){
            $users = $users->paginate($this->limit);
        }else{
            $users = $users->get();
        }


        return $this->throwMessage(200, 'success', "User without this role", $users);

        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    




    public function showUserByPermission(Request $request)
    {
        if (!$this->superAdminCheck()) {
            return $this->throwMessage(404, 'error', 'Permission denied, Only superadmin can access!');
        }
        if (!$this->checkIfExistPermission($request->permission_id)) {
            return $this->throwMessage(404, 'error', 'Permission is not exist!');
        }
        try {
            $data = Permission::where('id', $request->permission_id)->pluck('name');
            // // return $data;
            // $users = User::role($data[0])->get();
            $users = User::permission($data[0])->get();
            return $this->throwMessage(200, 'success', "Permission information", $users);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    public function roleViaPermission(Request $request)
    {
        if (!$this->superAdminCheck()) {
            return $this->throwMessage(404, 'error', 'Permission denied, Only superadmin can access!');
        }
        try {
            $role = Role::find($request->role_id);
            $permissions = $role->permissions->pluck('name', 'id');
            return $this->throwMessage(200, 'success', "All Permission information", $permissions);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    public function getSingleUser(Request $request)
    {
        try {
            $user = User::find($request->id);
            return $this->throwMessage(200, 'success', "Single User information", $user);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    public function setRoleUserList(Request $request)
    {
        try {
            $data = Role::where('id', $request->id)->pluck('name');
            $users = User::role($data[0])->get()->pluck('id');
            $allUserData = User::whereNotIn('id', $users)->get();
            return $this->throwMessage(200, 'success', "Role information", $allUserData);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    public function importUserGroup(Request $request)
    {
        $this->validate($request, [
            'excel_file' => 'required|mimes:csv,txt,xls,xlsx'
        ]);
        DB::beginTransaction();
        try {
            $inputFileName = $request->file('excel_file')->getRealPath();
            $spreadsheet = IOFactory::load($inputFileName);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $msg = $this->uploadUserBulkDataFromCSV($sheetData);
            if ($msg) {
                return $this->throwMessage(413, 'error', $msg);
            }
            DB::commit();
            // dd($sheetData);
            // Excel::import(new UserGroupImport(), request()->file('excel_file'));
            return $this->throwMessage(200, 'success', "Import file successfully");
        } catch (\Exception $e) {
            DB::rollback();
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }

    public function checkColumn($row)
    {
        foreach ($row as $val) {
            $val = trim(strtolower($val), ' ');
            if ($val == 'username' || $val == 'name' || $val == 'email' || $val == 'phone' || $val == 'user_type' || $val == 'status' || $val == 'supervisor_username' || $val == 'user_group' || $val == 'gender' || $val == 'date_of_joining') {
                $msg = false;
            } else {
                $msg =  $val . " column is not correct. Correct Format is: username, name, email, phone, user_type, status, supervisor_username, user_group, date_of_joining";
                break;
            }
        }
        return $msg;
    }

    public function getUserByUsername($username)
    {
        return User::where('username', $username)->first();
    }

    public function uploadUserBulkDataFromCSV($data)
    {
        User::where('status', 'active')->update([
            'status' => 'inactive'
        ]);
        $flag = 0;
        foreach ($data as $key => $row) {
            // if ($flag == 0) {
            //     $flag = 1;
            //     $msg = $this->checkColumn($row);
            //     if ($msg) {
            //         return $msg;
            //         break;
            //     }
            //     continue;
            // }
            $processes = $this->getProcesses($row['G']); //fetch all process from excel
            $uData = $this->getUserByUsername($row['A']); //fetch user by username
            if ($row['H']) {
                $supervisor = $this->getUserByUsername($row['H']); // check if supervisor is exist in user or return null
            } else {
                $supervisor = null;
            }
            if ($uData) {
                $user = User::where('username', $row['A'])->update([
                    'name' => $row['B'],
                    'user_type' => $row['F'],
                    'status' => $row['I'],
                    'date_of_joining' => date('Y-m-d', strtotime($row['J'])),
                ]);
                $user = $uData;
            } else {
                // dd($row[1]);
                $user = User::create([
                    'username' => $row['A'],
                    'name' => $row['B'],
                    'phone' => $row['C'],
                    'email' => $row['D'],
                    'gender' => $row['E'],
                    'user_type' => $row['F'],
                    'status' => $row['I'],
                    'date_of_joining' => date('Y-m-d', strtotime($row['J'])),
                    // 'supervisor' => $supervisor->id,
                    'password' => Hash::make(env("AGENT_PASSWORD")),
                ]);
            }

            if ($supervisor && ($user->id != $supervisor->id)) {
                Supervisor::create([
                    'user_id' => $user->id,
                    'supervisor_id' => $supervisor->id,
                ]);
            }
            foreach ($processes as  $process_id) {
                $process = AgentProcess::where('user_id', $user->id)->where('process_id', $process_id)->exists();
                if (!$process) {
                    $user->agentProcess()->create(['process_id' => $process_id]);
                }
            }
        }
        return false;
    }

    function getProcesses($names)
    {
        foreach ($this->multiexplode(array(", ", ",", " ,", " , "), $names) as $name) {
            $data = Process::whereName($name)->first();
            if ($data) {
                $processes[] = $data->id;
            } else {
                $p = Process::create([
                    'name' => $name,
                ]);
                $processes[] = $p->id;
            }
        }
        return $processes;
    }

    function getUserId($id)
    {
        return User::find($id);
    }

    function multiexplode($delimiters, $string)
    {

        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return  $launch;
    }

    function getUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function editProfile(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'name' => 'required',
            'phone' => 'required',
        ]);
        try {
            $user = User::find($request->id);
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->address = $request->address ? $request->address : $user->address;
            $user->save();
            return $this->throwMessage(200, 'success', "User information updated successfully", $user);
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }
}
