<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolesAccesoTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ─────────────────────────────────────────────

    private function usuario(string $rol): Usuario
    {
        $empresa = Empresa::factory()->create();
        return Usuario::factory()->create(['empresa_id' => $empresa->id, 'rol' => $rol]);
    }

    // ── Acceso al dashboard ──────────────────────────────────

    public function test_usuario_no_autenticado_redirige_a_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_usuario_activo_accede_al_dashboard(): void
    {
        $this->actingAs($this->usuario('Empleado'))
            ->get(route('dashboard'))
            ->assertOk();
    }

    // ── Gestión de empresas ──────────────────────────────────

    public function test_superadmin_puede_acceder_a_gestion_empresas(): void
    {
        $this->actingAs($this->usuario('Superadmin'))
            ->get(route('empresas.index'))
            ->assertOk();
    }

    public function test_empleado_no_puede_acceder_a_gestion_empresas(): void
    {
        $this->actingAs($this->usuario('Empleado'))
            ->get(route('empresas.index'))
            ->assertForbidden();
    }

    public function test_admin_no_puede_acceder_a_gestion_empresas(): void
    {
        $this->actingAs($this->usuario('Admin'))
            ->get(route('empresas.index'))
            ->assertForbidden();
    }

    // ── Gestión de usuarios ──────────────────────────────────

    public function test_admin_puede_ver_lista_de_usuarios(): void
    {
        $this->actingAs($this->usuario('Admin'))
            ->get(route('usuarios.index'))
            ->assertOk();
    }

    public function test_empleado_no_puede_ver_lista_de_usuarios(): void
    {
        $this->actingAs($this->usuario('Empleado'))
            ->get(route('usuarios.index'))
            ->assertForbidden();
    }

    // ── Reportes de actividad ────────────────────────────────

    public function test_superadmin_puede_ver_reportes(): void
    {
        $this->actingAs($this->usuario('Superadmin'))
            ->get(route('reportes.actividad'))
            ->assertOk();
    }

    public function test_aux_qhse_puede_ver_reportes(): void
    {
        $this->actingAs($this->usuario('Aux_QHSE'))
            ->get(route('reportes.actividad'))
            ->assertOk();
    }

    public function test_admin_no_puede_ver_reportes(): void
    {
        $this->actingAs($this->usuario('Admin'))
            ->get(route('reportes.actividad'))
            ->assertForbidden();
    }

    public function test_empleado_no_puede_ver_reportes(): void
    {
        $this->actingAs($this->usuario('Empleado'))
            ->get(route('reportes.actividad'))
            ->assertForbidden();
    }

    // ── Permisos globales ─────────────────────────────────────

    public function test_superadmin_puede_ver_permisos_globales(): void
    {
        $this->actingAs($this->usuario('Superadmin'))
            ->get(route('permisos.global'))
            ->assertOk();
    }

    public function test_empleado_no_puede_ver_permisos_globales(): void
    {
        $this->actingAs($this->usuario('Empleado'))
            ->get(route('permisos.global'))
            ->assertForbidden();
    }

    // ── Helpers de rol en el modelo ──────────────────────────

    public function test_helpers_de_rol_devuelven_correcto(): void
    {
        $sa  = Usuario::factory()->make(['rol' => 'Superadmin']);
        $adm = Usuario::factory()->make(['rol' => 'Admin']);
        $emp = Usuario::factory()->make(['rol' => 'Empleado']);

        $this->assertTrue($sa->esSuperAdmin());
        $this->assertFalse($sa->esAdmin());

        $this->assertTrue($adm->esAdmin());
        $this->assertFalse($adm->esSuperAdmin());

        $this->assertFalse($emp->esSuperAdmin());
        $this->assertFalse($emp->esAdmin());
    }
}
