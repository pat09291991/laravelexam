<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class UserTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $query = '?q=&page=1&sizePerPage=10&sortField=id&sortOrder=desc&roles=';
        $response = $this->get('api/v1/users' . $query);
        $response->dump();

        $response->assertStatus(200);
    }

    public function testStore()
    {
        $requestBody = [
            'full_name' => "John Doe",
            'email_address' => 'johndoe@email.com',
            'password' => 'test1234',
            'password_confirmation' => 'test1234',
            'roles' => [1, 2]
        ];

        $response = $this->post('api/v1/users', $requestBody);
        $response->dump();
        $response->assertStatus(200);
    }

    public function testShow()
    {
        $response = $this->get('api/v1/users/1');
        $response->dump();
        $response->assertStatus(200);
    }

    public function testUpdate()
    {
        $requestBody = [
            'full_name' => "John Doe",
            'email_address' => 'johndoe@email.com',
            'password' => 'test12345',
            'password_confirmation' => 'test1234',
            'roles' => [2, 3]
        ];

        $response = $this->put('api/v1/users/1', $requestBody);
        $response->dump();
        $response->assertStatus(200);
    }

    public function testFilters()
    {
        $response = $this->get('/api/v1/filters');
        $response->dump();

        $response->assertStatus(200);
    }
}
