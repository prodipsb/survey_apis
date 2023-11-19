<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use Laravel\Passport\Passport;

class AccessTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if ($request->header('Authorization')) {
            // Extract the token from the Authorization header
            $headerValue = $request->header('Authorization');
            $token = str_replace('Bearer ', '', $headerValue);
            // dd($token);

            $request->request->add(['access_token' => $token]);

            // Attach the token to the request
            // $request->attributes->add(['access_token' => $token]);

            // dd($request->all());

            $rr = Passport::token()->where('id', $token)->exists();
             dd($rr, $token);

            if (!Passport::token()->where('id', $token)->exists()) {
                throw new AuthenticationException('Invalid access token');
            }
            
        }

        return $next($request);

      
    

        //  if (!Auth::guard('api')->check()) {
        //      return response()->json(['error' => 'Invalid access token'], 401);
        //  }

        // if (Auth::guard('api')->check()) {
        //     return $next($request);
        // }

        // throw new AuthenticationException('Unauthenticated');

        //return $next($request);

         // Check if the access token is present in the request
          $accessToken = $request->access_token;
        //   dd($accessToken);

         if (!$accessToken) {
             return response()->json(['error' => 'Unauthorized'], 401);
         }
 
         // You can validate the access token here (e.g., using Passport's token validation)
         // For example, you can use Passport's token repository:
        //  if (!\DB::table('oauth_access_tokens')->where('id', $accessToken)->exists()) {
        //      return response()->json(['error' => 'Invalid access token'], 401);
        //  }
 
         // Alternatively, you can use Passport's built-in helper:
         if (!Auth::guard('api')->check()) {
             return response()->json(['error' => 'Invalid access token'], 401);
         }
 
         // The access token is valid, continue with the request         
         return $next($request);
    }
}
