<?php

namespace Tests\Feature\Auth;

use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $empresa = Empresa::factory()->create();
        $user    = Usuario::factory()->create(['empresa_id' => $empresa->id]);

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $empresa = Empresa::factory()->create();
        $user    = Usuario::factory()->create(['empresa_id' => $empresa->id]);

        $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $empresa = Empresa::factory()->create();
        $user    = Usuario::factory()->create(['empresa_id' => $empresa->id]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
