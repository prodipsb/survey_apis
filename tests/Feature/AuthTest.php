<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // public function test_example()
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }


        /**
     * Test auth login endpoint.
     *
     * @return void
     */
    public function testAuthLogin()
    {
        // Mock request data
        $requestData = [
            'email' => 500021,
            'password' => '123456',
            'login_mode' => 'web login',
        ];

        // Send POST request to auth-login endpoint
        $response = $this->postJson('/api/v1/auth-login', $requestData);

        // Assert response status code
        $response->assertStatus(200);

        // Assert response JSON structure
        $response->assertJsonStructure([
            'token_type',
            'access_token',
            'user' => [
                'id',
                'employee_id',
                'role_id',
                'name',
                'avatar',
            ],
        ]);

        // Assert other response data if needed
        // $response->assertJson(...);

        // You can also assert database changes, session data, cookies, etc.
    }

}
