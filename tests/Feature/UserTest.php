<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get('/api/users');

        $response->assertStatus(200);
    }

    public function testStore()
    {
        $requestBody = [
            'full_name' => "John Doe",
            'email' => 'johndoe@email.com',
            'roles' => [1, 2]
        ];

        $response = $this->post('api/users', $requestBody);
        $response->dump();
        $response->assertStatus(200);
    }

    public function testShow()
    {
        $response = $this->get('api/users/1');
        $response->dump();
        $response->assertStatus(200);
    }

    public function testUpdate()
    {
        $requestBody = [
            'full_name' => "John Doe",
            'email' => 'johndoe@email.com',
            'roles' => [2, 3]
        ];

        $response = $this->put('api/users/1', $requestBody);
        $response->dump();
        $response->assertStatus(200);
    }
}
