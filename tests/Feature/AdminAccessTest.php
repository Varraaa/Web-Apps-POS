<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_redirects_to_the_admin_dashboard(): void
    {
        User::factory()->create([
            'email' => 'admin@varstore',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'admin@varstore',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_cannot_open_the_buyer_collection(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('collection'));

        $response->assertRedirect(route('admin.dashboard'));
    }
}
