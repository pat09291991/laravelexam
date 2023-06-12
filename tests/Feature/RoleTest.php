<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use WithoutMiddleware;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $query = '?q=admin&page=1&sizePerPage=10&sortField=id&sortOrder=desc';
        $response = $this->get('api/v1/roles' . $query);
        $response->dump();

        $response->assertStatus(200);
    }

    public function testStore()
    {
        $requestBody = [
            'name' => "Test Role",
        ];

        $response = $this->post('api/v1/roles', $requestBody);
        $response->dump();
        $response->assertStatus(200);
    }

    public function testShow()
    {
        $response = $this->get('api/v1/roles/1');
        $response->dump();
        $response->assertStatus(200);
    }

    public function testUpdate()
    {
        $requestBody = [
            'name' => "Test Role1",
        ];

        $response = $this->put('api/v1/roles/5', $requestBody);
        $response->dump();
        $response->assertStatus(200);
    }
}
