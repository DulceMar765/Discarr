<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login()
    {
        $response = $this->get(route('projects.index'));
        $response->assertRedirect('/login');

        $response = $this->get(route('projects.create'));
        $response->assertRedirect('/login');

        $response = $this->get(route('profile.edit'));
        $response->assertRedirect('/login');
    }

    public function test_non_admin_user_cannot_access_admin_routes()
    {
        // Crear un usuario normal (no admin)
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $response = $this->actingAs($user)->get(route('projects.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->get(route('projects.create'));
        $response->assertStatus(403);
    }

    public function test_admin_user_can_access_admin_routes()
    {
        // Crear un usuario administrador
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this->actingAs($admin)->get(route('projects.index'));
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get(route('projects.create'));
        $response->assertStatus(200);
    }
}
