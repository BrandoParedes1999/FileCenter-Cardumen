<?php

namespace Tests\Feature;

use App\Models\Carpeta;
use App\Models\Empresa;
use App\Models\PermisoCarpeta;
use App\Models\SolicitudAcceso;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolicitudAccesoTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ─────────────────────────────────────────────

    private function crearEscenarioCrossEmpresa(): array
    {
        $empresaA  = Empresa::factory()->create(['es_corporativo' => false]);
        $empresaB  = Empresa::factory()->create(['es_corporativo' => false]);
        $adminA    = Usuario::factory()->create(['empresa_id' => $empresaA->id, 'rol' => 'Admin']);
        $empleadoB = Usuario::factory()->create(['empresa_id' => $empresaB->id, 'rol' => 'Empleado']);
        $carpeta   = Carpeta::factory()->create(['empresa_id' => $empresaA->id, 'es_publico' => false]);

        return compact('empresaA', 'empresaB', 'adminA', 'empleadoB', 'carpeta');
    }

    // ── Creación ─────────────────────────────────────────────

    public function test_empleado_puede_crear_solicitud_de_acceso(): void
    {
        ['empresaA' => $empresaA, 'empleadoB' => $empleadoB, 'carpeta' => $carpeta] = $this->crearEscenarioCrossEmpresa();

        $this->actingAs($empleadoB)
            ->post(route('solicitudes.store'), [
                'empresa_objetivo_id' => $empresaA->id,
                'carpeta_id'          => $carpeta->id,
                'razon'               => 'Necesito acceder para el proyecto X',
                'tipo_acceso'         => 'Lectura',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('solicitudes_acceso', [
            'solicitante_id'      => $empleadoB->id,
            'empresa_objetivo_id' => $empresaA->id,
            'carpeta_id'          => $carpeta->id,
            'status'              => 'Pendiente',
        ]);
    }

    // ── Listado ──────────────────────────────────────────────

    public function test_admin_ve_solicitudes_de_su_empresa(): void
    {
        ['empresaA' => $empresaA, 'adminA' => $adminA, 'empleadoB' => $empleadoB, 'carpeta' => $carpeta] = $this->crearEscenarioCrossEmpresa();

        SolicitudAcceso::factory()->create([
            'solicitante_id'      => $empleadoB->id,
            'empresa_objetivo_id' => $empresaA->id,
            'carpeta_id'          => $carpeta->id,
        ]);

        $this->actingAs($adminA)
            ->get(route('solicitudes.index'))
            ->assertOk()
            ->assertSeeText($empleadoB->nombre);
    }

    public function test_empleado_no_ve_solicitudes_ajenas(): void
    {
        ['empresaA' => $empresaA, 'adminA' => $adminA, 'empleadoB' => $empleadoB, 'carpeta' => $carpeta] = $this->crearEscenarioCrossEmpresa();

        $otraEmp = Empresa::factory()->create();
        $otroEmp = Usuario::factory()->create(['empresa_id' => $otraEmp->id, 'rol' => 'Empleado']);

        SolicitudAcceso::factory()->create([
            'solicitante_id'      => $empleadoB->id,
            'empresa_objetivo_id' => $empresaA->id,
            'carpeta_id'          => $carpeta->id,
        ]);

        // El otro empleado solo ve SUS solicitudes
        $this->actingAs($otroEmp)
            ->get(route('solicitudes.index'))
            ->assertOk()
            ->assertDontSeeText($empleadoB->nombre);
    }

    // ── Aprobación ────────────────────────────────────────────

    public function test_admin_aprueba_solicitud_y_crea_permiso(): void
    {
        ['empresaA' => $empresaA, 'adminA' => $adminA, 'empleadoB' => $empleadoB, 'carpeta' => $carpeta] = $this->crearEscenarioCrossEmpresa();

        $solicitud = SolicitudAcceso::factory()->create([
            'solicitante_id'      => $empleadoB->id,
            'empresa_objetivo_id' => $empresaA->id,
            'carpeta_id'          => $carpeta->id,
            'status'              => 'Pendiente',
            'tipo_acceso'         => 'Lectura',
        ]);

        $this->actingAs($adminA)
            ->post(route('solicitudes.aprobar', $solicitud))
            ->assertRedirect();

        $this->assertDatabaseHas('solicitudes_acceso', [
            'id'     => $solicitud->id,
            'status' => 'Aprobado',
        ]);

        $this->assertDatabaseHas('permisos_de_carpeta', [
            'carpeta_id' => $carpeta->id,
            'usuario_id' => $empleadoB->id,
            'puede_leer' => 1,
        ]);
    }

    public function test_aprobacion_tipo_descargar_crea_permiso_con_descarga(): void
    {
        ['empresaA' => $empresaA, 'adminA' => $adminA, 'empleadoB' => $empleadoB, 'carpeta' => $carpeta] = $this->crearEscenarioCrossEmpresa();

        $solicitud = SolicitudAcceso::factory()->create([
            'solicitante_id'      => $empleadoB->id,
            'empresa_objetivo_id' => $empresaA->id,
            'carpeta_id'          => $carpeta->id,
            'status'              => 'Pendiente',
            'tipo_acceso'         => 'Descargar',
        ]);

        $this->actingAs($adminA)
            ->post(route('solicitudes.aprobar', $solicitud))
            ->assertRedirect();

        $this->assertDatabaseHas('permisos_de_carpeta', [
            'carpeta_id'      => $carpeta->id,
            'usuario_id'      => $empleadoB->id,
            'puede_descargar' => 1,
        ]);
    }

    // ── Rechazo ───────────────────────────────────────────────

    public function test_admin_puede_rechazar_solicitud(): void
    {
        ['empresaA' => $empresaA, 'adminA' => $adminA, 'empleadoB' => $empleadoB, 'carpeta' => $carpeta] = $this->crearEscenarioCrossEmpresa();

        $solicitud = SolicitudAcceso::factory()->create([
            'solicitante_id'      => $empleadoB->id,
            'empresa_objetivo_id' => $empresaA->id,
            'carpeta_id'          => $carpeta->id,
            'status'              => 'Pendiente',
        ]);

        $this->actingAs($adminA)
            ->post(route('solicitudes.rechazar', $solicitud), [
                'comentario_revisor' => 'No aplica para este recurso.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('solicitudes_acceso', [
            'id'     => $solicitud->id,
            'status' => 'Rechazado',
        ]);

        // No debe crearse permiso
        $this->assertDatabaseMissing('permisos_de_carpeta', [
            'carpeta_id' => $carpeta->id,
            'usuario_id' => $empleadoB->id,
        ]);
    }

    public function test_empleado_externo_no_puede_aprobar_solicitudes(): void
    {
        ['empresaA' => $empresaA, 'empleadoB' => $empleadoB, 'carpeta' => $carpeta] = $this->crearEscenarioCrossEmpresa();

        $solicitud = SolicitudAcceso::factory()->create([
            'solicitante_id'      => $empleadoB->id,
            'empresa_objetivo_id' => $empresaA->id,
            'carpeta_id'          => $carpeta->id,
            'status'              => 'Pendiente',
        ]);

        $this->actingAs($empleadoB)
            ->post(route('solicitudes.aprobar', $solicitud))
            ->assertForbidden();
    }

    // ── Solicitud ya procesada ────────────────────────────────

    public function test_no_se_puede_aprobar_solicitud_ya_procesada(): void
    {
        ['empresaA' => $empresaA, 'adminA' => $adminA, 'empleadoB' => $empleadoB, 'carpeta' => $carpeta] = $this->crearEscenarioCrossEmpresa();

        $solicitud = SolicitudAcceso::factory()->create([
            'solicitante_id'      => $empleadoB->id,
            'empresa_objetivo_id' => $empresaA->id,
            'carpeta_id'          => $carpeta->id,
            'status'              => 'Rechazado',
        ]);

        $this->actingAs($adminA)
            ->post(route('solicitudes.aprobar', $solicitud))
            ->assertRedirect();

        // El status no debe cambiar a Aprobado
        $this->assertDatabaseHas('solicitudes_acceso', [
            'id'     => $solicitud->id,
            'status' => 'Rechazado',
        ]);
    }

    // ── Modelo: método aprobar/rechazar ──────────────────────

    public function test_metodo_aprobar_en_modelo_crea_permiso_y_actualiza_status(): void
    {
        ['empresaA' => $empresaA, 'adminA' => $adminA, 'empleadoB' => $empleadoB, 'carpeta' => $carpeta] = $this->crearEscenarioCrossEmpresa();

        $solicitud = SolicitudAcceso::factory()->create([
            'solicitante_id'      => $empleadoB->id,
            'empresa_objetivo_id' => $empresaA->id,
            'carpeta_id'          => $carpeta->id,
            'tipo_acceso'         => 'Lectura',
            'status'              => 'Pendiente',
        ]);

        $solicitud->aprobar($adminA->id, 'Aprobado correctamente');

        $this->assertEquals('Aprobado', $solicitud->fresh()->status);
        $this->assertNotNull($solicitud->fresh()->revisado_en);

        $this->assertDatabaseHas('permisos_de_carpeta', [
            'carpeta_id' => $carpeta->id,
            'usuario_id' => $empleadoB->id,
        ]);
    }

    public function test_metodo_rechazar_en_modelo_actualiza_status(): void
    {
        ['adminA' => $adminA, 'empleadoB' => $empleadoB, 'carpeta' => $carpeta, 'empresaA' => $empresaA] = $this->crearEscenarioCrossEmpresa();

        $solicitud = SolicitudAcceso::factory()->create([
            'solicitante_id'      => $empleadoB->id,
            'empresa_objetivo_id' => $empresaA->id,
            'carpeta_id'          => $carpeta->id,
            'status'              => 'Pendiente',
        ]);

        $solicitud->rechazar($adminA->id, 'Motivo de rechazo');

        $this->assertEquals('Rechazado', $solicitud->fresh()->status);
        $this->assertEquals('Motivo de rechazo', $solicitud->fresh()->comentario_revisor);
    }
}
