<?php

namespace App\Http\Controllers;

use App\Http\Traits\GlobalTraits;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use App\Utilities\ProxyRequest;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use GlobalTraits;

    protected $model = 'User';
    protected $uploadDir = 'uploads/avatar';

    protected $proxy;

    public function __construct(ProxyRequest $proxy)
    {
        $this->proxy = $proxy;
    }



    protected function setHttpOnlyCookie(string $refreshToken)
    {
        cookie()->queue(
            'refresh_token',
            $refreshToken,
            43200, // 10 days
            null,
            null,
            false,
            false // httponly
        );
    }


    public function authlogin(Request $request)
    {

       $inputs = $request->except(['login_mode']);

       $rules = [
        'email' => [
            'required'
        ],
        'password' => [
            'required',
            'string',
            'min:6', // must be at least 6 characters in length
        ],
    ];
    
        $validation = Validator::make( $inputs, $rules );
    
        if ( $validation->fails() ) {
            return $this->throwMessage(400, 'error', $validation->errors());
        }
       
       
         Auth::shouldUse('web');

         if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $auth = User::where('email', $request->email)->first();
         }else{
            $inputs['employee_id'] = $inputs['email'];
            unset($inputs['email']);
            $auth = User::where('employee_id', $request->email)->first();
         }

         if(!$auth){
            return $this->throwMessage(400, 'error', "User not found!");
         }

         if($auth->login_attempts == 3 && $auth->last_login_attempted_at >= Carbon::now('Asia/Dhaka')->subMinutes(2)){
            return $this->throwMessage(400, 'error', "You have been blocked for \n2 minutes");
        }else if($auth->login_attempts == 3 && $auth->last_login_attempted_at < Carbon::now('Asia/Dhaka')->subMinutes(2)){
            $auth->login_attempts = 0;
            $auth->last_login_attempted_at = null;
            $auth->save();
        }


        if(Auth::attempt($inputs)){

            $auth = Auth::user();
            
            if(!$auth->roles->isEmpty() && $auth->roles[0]->hasPermissionTo($request->login_mode) == false){
                return $this->throwMessage(401, 'error', "Permission denied, You don't have {$request->login_mode} permission");
            }
          
            $token = $auth->createToken('authToken')->accessToken;
            $refreshToken = $auth->createToken('refreshToken')->accessToken;


            $this->setHttpOnlyCookie($refreshToken);

            $user = User::findOrFail(Auth::id());;
            $user->last_login = Carbon::now();
            $user->status = 'Active';
            $user->save();
    

            return response()->json([
                'token_type' => 'Bearer Token',
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'employee_id' => $user->employee_id,
                    'role_id' => $user->role_id,
                    'name' => $user->name,
                    'avatar' => $user->avatar
                ]
                ])
                ->cookie('access_token', $token, 600);

        }else {
           
            if ($auth->login_attempts <= 2) {
                 $auth->increment('login_attempts');
                 $auth->last_login_attempted_at = now('Asia/Dhaka');
                 $auth->save();

                 return $this->throwMessage(400, 'error', 'User Crediential Missmatch!');
                 
            } else {
                return $this->throwMessage(400, 'error', "Maximum Login Attempt Exceeded.\nTry Again After 2 Minutes.");

            }

           

        }

    }



    public function refreshToken(Request $request){
       // $token = $request->input('token');
       // dd($token);

       $http = new \GuzzleHttp\Client;

        $response = $http->post('http://4.193.55.34:8080/oauth/token', [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => 'the-refresh-token',
                'client_id' => ENV('client_id'),
                'client_secret' => 'client-secret',
                'scope' => '',
            ],
        ]);

        return json_decode((string) $response->getBody(), true);

    }


    
    public function logout()
    {

        try {
            User::where('id', auth()->user()->id)->update([
                'status' => 'inactive',
                'last_logout' => Carbon::now()
            ]);
            auth()->user()->tokens()->each(function ($token) {
                $token->delete();
            });
            return $this->throwMessage(200, 'success', 'Logged out successfully');
        } catch (\Exception $e) {
            return $this->throwMessage(413, 'error', $e->getMessage());
        }
    }



}
