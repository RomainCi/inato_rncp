<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_de_la_route_forget_password()
    {
        $user = User::first();
        $response = $this->post('api/forget', [
            "email" => $user->email
        ]);
        $response->assertStatus(200);
    }
}
