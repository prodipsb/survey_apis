<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
             return route('login');
        }
    }

    protected function unauthenticated($request, array $guards)
    {
        abort(response()->json(['error' => 'Unauthenticated.'], 401));
    }

    

    // protected function authenticate(array $guards)
    // {
    //     if (empty($guards)) {
    //         return $this->auth->authenticate();
    //     }

    //     foreach ($guards as $guard) {
    //         if ($this->auth->guard($guard)->check()) {
    //             return $this->auth->shouldUse($guard);
    //         }
    //     }

    //     throw new AuthenticationException('Unauthenticated.', $guards);
    // }
    
}
