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
    public function testAuth()
    {
        $requestBody = [
            'email_address' => 'johndoe@email.com',
            'password' => 'test12345'
        ];

        $response = $this->post('api/v1/auth', $requestBody);
        $response->dump();
        $response->assertStatus(200);
    }
}
