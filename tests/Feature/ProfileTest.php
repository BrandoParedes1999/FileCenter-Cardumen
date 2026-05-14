<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function crearUsuario(): Usuario
    {
        $empresa = Empresa::factory()->create();
        return Usuario::factory()->create(['empresa_id' => $empresa->id]);
    }

    public function test_profile_page_is_displayed(): void
    {
        $this->actingAs($this->crearUsuario())
            ->get('/profile')
            ->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = $this->crearUsuario();

        $this->actingAs($user)
            ->patch('/profile', [
                'nombre'  => 'Nuevo',
                'paterno' => 'Apellido',
                'materno' => 'Materno',
                'email'   => 'nuevo@example.com',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();
        $this->assertSame('Nuevo', $user->nombre);
        $this->assertSame('nuevo@example.com', $user->email);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = $this->crearUsuario();

        $this->actingAs($user)
            ->from('/profile')
            ->delete('/profile', ['password' => 'wrong-password'])
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
