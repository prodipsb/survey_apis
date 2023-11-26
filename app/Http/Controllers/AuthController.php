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
       // dd($inputs, $request->login_mode);

        $rules = [
            'email'    => 'required|email',
            'password' => [
                'required',
                'string',
                'min:6',             // must be at least 10 characters in length
                //'regex:/[a-z]/',      // must contain at least one lowercase letter
               // 'regex:/[A-Z]/',      // must contain at least one uppercase letter
               // 'regex:/[0-9]/',      // must contain at least one digit
               // 'regex:/[@$!%*#?&]/', // must contain a special character
            ],
        ];
    
        $validation = Validator::make( $inputs, $rules );
    
        if ( $validation->fails() ) {
            return $validation->errors(); 
        }
       

         Auth::shouldUse('web');

        if(Auth::attempt($inputs)){

            $auth = Auth::user();

            // dd($auth->roles[0]->hasPermissionTo($request->login_mode));

            if(!$auth->roles->isEmpty() && $auth->roles[0]->hasPermissionTo($request->login_mode) == false){
                return $this->throwMessage(401, 'error', "Permission denied, You don't have {$request->login_mode} permission");
            }

          
            $token = $auth->createToken('authToken')->accessToken;
            $refreshToken = $auth->createToken('refreshToken')->accessToken;


            $this->setHttpOnlyCookie($refreshToken);

            $user = User::findOrFail(Auth::id());;
            $user->last_login = Carbon::now();
            $user->status = 'active';
            $user->save();
    

            return response()->json([
                'token_type' => 'Bearer Token',
                'access_token' => $token,
                ])
                ->cookie('access_token', $token, 600);

        }else{
            return $this->throwMessage(200, 'error', 'User crediential mismatch');
        };

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
