<?php

namespace Tests\Feature\Auth;

use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    private function crearUsuario(): Usuario
    {
        $empresa = Empresa::factory()->create();
        return Usuario::factory()->create(['empresa_id' => $empresa->id]);
    }

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $user = $this->crearUsuario();

        $this->actingAs($user)->get('/confirm-password')->assertStatus(200);
    }

    public function test_password_can_be_confirmed(): void
    {
        $user = $this->crearUsuario();

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $user = $this->crearUsuario();

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}
