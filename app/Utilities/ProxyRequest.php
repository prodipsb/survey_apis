<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Request;

class ProxyRequest
{
    public function grantPasswordToken(string $email, string $password)
    {
        $params = [
            'grant_type' => 'password',
            'username' => $email,
            'password' => $password,
        ];

        // dd('$params', $params);

        return $this->makePostRequest($params);
    }

    public function refreshAccessToken()
    {
        $refreshToken = request()->cookie('refresh_token');

        abort_unless($refreshToken, 403, 'Your refresh token is expired.');

        $params = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ];

        return $this->makePostRequest($params);
    }

    protected function makePostRequest(array $params)
    {
        $params = array_merge([
            'client_id' => config('services.passport.password_client_id'),
            'client_secret' => config('services.passport.password_client_secret'),
            'scope' => '*',
        ], $params);
     
       // $token = json_decode((string) $response->getBody(), true);
       // dd($token);

        $proxy = Request::create('oauth/token', 'post', $params);
         dd('$proxy', $proxy);
        $resp = json_decode(app()->handle($proxy)->getContent());
         dd('$resp', $resp);

        $this->setHttpOnlyCookie($resp->refresh_token);

        dd('$resp', $resp);
        return $resp;
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
            true // httponly
        );
    }
}