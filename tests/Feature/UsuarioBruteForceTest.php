<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuarioBruteForceTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ─────────────────────────────────────────────

    private function crearUsuario(array $attrs = []): Usuario
    {
        $empresa = Empresa::factory()->create();
        return Usuario::factory()->create(array_merge(['empresa_id' => $empresa->id], $attrs));
    }

    // ── Bloqueo ──────────────────────────────────────────────

    public function test_usuario_se_bloquea_despues_de_cinco_intentos_fallidos(): void
    {
        $usuario = $this->crearUsuario();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), [
                'email'    => $usuario->email,
                'password' => 'password_incorrecta',
            ]);
        }

        $this->assertTrue($usuario->fresh()->estaBloqueado());
    }

    public function test_usuario_bloqueado_no_puede_iniciar_sesion(): void
    {
        $usuario = $this->crearUsuario([
            'bloqueado_hasta' => now()->addMinutes(15),
        ]);

        $this->post(route('login'), [
            'email'    => $usuario->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_usuario_desbloquea_cuando_tiempo_expira(): void
    {
        $usuario = $this->crearUsuario([
            'bloqueado_hasta' => now()->subMinute(),
        ]);

        $this->assertFalse($usuario->estaBloqueado());
    }

    // ── Incremento de intentos ───────────────────────────────

    public function test_incrementar_intentos_incrementa_contador(): void
    {
        $usuario = $this->crearUsuario(['intentos_login' => 0]);
        $usuario->incrementarIntentos();

        $this->assertEquals(1, $usuario->fresh()->intentos_login);
    }

    public function test_quinto_intento_bloquea_y_resetea_contador(): void
    {
        $usuario = $this->crearUsuario(['intentos_login' => 4]);
        $usuario->incrementarIntentos();

        $fresh = $usuario->fresh();
        $this->assertEquals(0, $fresh->intentos_login);
        $this->assertTrue($fresh->estaBloqueado());
    }

    // ── Reset de intentos ────────────────────────────────────

    public function test_resetear_intentos_limpia_bloqueo(): void
    {
        $usuario = $this->crearUsuario([
            'intentos_login'  => 3,
            'bloqueado_hasta' => now()->addMinutes(15),
        ]);

        $usuario->resetearIntentos();

        $fresh = $usuario->fresh();
        $this->assertEquals(0, $fresh->intentos_login);
        $this->assertNull($fresh->bloqueado_hasta);
        $this->assertFalse($fresh->estaBloqueado());
    }

    public function test_login_exitoso_resetea_intentos(): void
    {
        $usuario = $this->crearUsuario(['intentos_login' => 3]);

        $this->post(route('login'), [
            'email'    => $usuario->email,
            'password' => 'password',
        ]);

        $this->assertEquals(0, $usuario->fresh()->intentos_login);
        $this->assertAuthenticatedAs($usuario);
    }

    // ── Desbloqueo manual (Admin) ────────────────────────────

    public function test_admin_puede_desbloquear_usuario(): void
    {
        $empresa  = Empresa::factory()->create();
        $admin    = Usuario::factory()->create(['empresa_id' => $empresa->id, 'rol' => 'Admin']);
        $bloqueado = Usuario::factory()->create([
            'empresa_id'      => $empresa->id,
            'bloqueado_hasta' => now()->addMinutes(15),
        ]);

        $this->actingAs($admin)
            ->post(route('usuarios.desbloquear', $bloqueado))
            ->assertRedirect();

        $this->assertFalse($bloqueado->fresh()->estaBloqueado());
    }

    public function test_empleado_no_puede_desbloquear_usuarios(): void
    {
        $empresa   = Empresa::factory()->create();
        $empleado  = Usuario::factory()->create(['empresa_id' => $empresa->id, 'rol' => 'Empleado']);
        $bloqueado = Usuario::factory()->create([
            'empresa_id'      => $empresa->id,
            'bloqueado_hasta' => now()->addMinutes(15),
        ]);

        $this->actingAs($empleado)
            ->post(route('usuarios.desbloquear', $bloqueado))
            ->assertForbidden();
    }
}
