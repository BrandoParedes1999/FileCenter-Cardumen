<?php

namespace Tests\Feature;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\Empresa;
use App\Models\SolicitudSubida;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SolicitudSubidaTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ─────────────────────────────────────────────

    private function crearEscenario(): array
    {
        $empresa  = Empresa::factory()->create(['es_corporativo' => false]);
        $admin    = Usuario::factory()->create(['empresa_id' => $empresa->id, 'rol' => 'Admin']);
        $empleado = Usuario::factory()->create(['empresa_id' => $empresa->id, 'rol' => 'Empleado']);
        $carpeta  = Carpeta::factory()->create([
            'empresa_id'                 => $empresa->id,
            'requiere_aprobacion_subida' => true,
        ]);

        return compact('empresa', 'admin', 'empleado', 'carpeta');
    }

    // ── Listado ──────────────────────────────────────────────

    public function test_admin_ve_solicitudes_subida_de_su_empresa(): void
    {
        ['empresa' => $empresa, 'admin' => $admin, 'empleado' => $empleado, 'carpeta' => $carpeta] = $this->crearEscenario();

        SolicitudSubida::factory()->create([
            'carpeta_id'     => $carpeta->id,
            'solicitante_id' => $empleado->id,
        ]);

        $this->actingAs($admin)
            ->get(route('solicitudes-subida.index'))
            ->assertOk()
            ->assertSeeText($empleado->nombre);
    }

    public function test_empleado_no_puede_ver_listado_de_solicitudes_subida_ajenas(): void
    {
        ['empresa' => $empresa, 'empleado' => $empleado, 'carpeta' => $carpeta] = $this->crearEscenario();

        $otraEmp       = Empresa::factory()->create();
        $otroEmpleado  = Usuario::factory()->create(['empresa_id' => $otraEmp->id, 'rol' => 'Empleado']);
        $otraCarpeta   = Carpeta::factory()->create(['empresa_id' => $otraEmp->id]);

        SolicitudSubida::factory()->create([
            'carpeta_id'     => $otraCarpeta->id,
            'solicitante_id' => $otroEmpleado->id,
        ]);

        $this->actingAs($empleado)
            ->get(route('solicitudes-subida.index'))
            ->assertOk()
            ->assertDontSeeText($otroEmpleado->nombre);
    }

    // ── Aprobación ────────────────────────────────────────────

    public function test_admin_aprueba_solicitud_y_se_crea_archivo(): void
    {
        Storage::fake('filecenter');

        ['empresa' => $empresa, 'admin' => $admin, 'empleado' => $empleado, 'carpeta' => $carpeta] = $this->crearEscenario();

        $rutaTemporal = 'temp/archivo-prueba.pdf';
        Storage::disk('filecenter')->put($rutaTemporal, 'contenido de prueba');

        $solicitud = SolicitudSubida::factory()->create([
            'carpeta_id'     => $carpeta->id,
            'solicitante_id' => $empleado->id,
            'ruta_temporal'  => $rutaTemporal,
            'nombre_original'=> 'documento.pdf',
            'extension'      => 'pdf',
            'status'         => 'Pendiente',
        ]);

        $this->actingAs($admin)
            ->post(route('solicitudes-subida.aprobar', $solicitud), [
                'comentario_revisor' => 'Todo correcto.',
            ])
            ->assertRedirect(route('solicitudes-subida.index'));

        $this->assertDatabaseHas('solicitudes_subida', [
            'id'     => $solicitud->id,
            'status' => 'Aprobado',
        ]);

        $this->assertDatabaseHas('archivos', [
            'carpeta_id'      => $carpeta->id,
            'nombre_original' => 'documento.pdf',
        ]);
    }

    public function test_aprobacion_falla_si_archivo_temporal_no_existe(): void
    {
        Storage::fake('filecenter');

        ['empresa' => $empresa, 'admin' => $admin, 'empleado' => $empleado, 'carpeta' => $carpeta] = $this->crearEscenario();

        $solicitud = SolicitudSubida::factory()->create([
            'carpeta_id'     => $carpeta->id,
            'solicitante_id' => $empleado->id,
            'ruta_temporal'  => 'temp/no-existe.pdf',
            'status'         => 'Pendiente',
        ]);

        $this->actingAs($admin)
            ->post(route('solicitudes-subida.aprobar', $solicitud))
            ->assertSessionHasErrors('error');
    }

    // ── Rechazo ───────────────────────────────────────────────

    public function test_admin_rechaza_solicitud_y_elimina_archivo_temporal(): void
    {
        Storage::fake('filecenter');

        ['empresa' => $empresa, 'admin' => $admin, 'empleado' => $empleado, 'carpeta' => $carpeta] = $this->crearEscenario();

        $rutaTemporal = 'temp/archivo-rechazar.pdf';
        Storage::disk('filecenter')->put($rutaTemporal, 'contenido');

        $solicitud = SolicitudSubida::factory()->create([
            'carpeta_id'     => $carpeta->id,
            'solicitante_id' => $empleado->id,
            'ruta_temporal'  => $rutaTemporal,
            'status'         => 'Pendiente',
        ]);

        $this->actingAs($admin)
            ->post(route('solicitudes-subida.rechazar', $solicitud), [
                'comentario_revisor' => 'El formato no es válido.',
            ])
            ->assertRedirect(route('solicitudes-subida.index'));

        $this->assertDatabaseHas('solicitudes_subida', [
            'id'     => $solicitud->id,
            'status' => 'Rechazado',
        ]);

        Storage::disk('filecenter')->assertMissing($rutaTemporal);
    }

    public function test_rechazo_requiere_comentario(): void
    {
        Storage::fake('filecenter');

        ['empresa' => $empresa, 'admin' => $admin, 'empleado' => $empleado, 'carpeta' => $carpeta] = $this->crearEscenario();

        $solicitud = SolicitudSubida::factory()->create([
            'carpeta_id'     => $carpeta->id,
            'solicitante_id' => $empleado->id,
            'status'         => 'Pendiente',
        ]);

        $this->actingAs($admin)
            ->post(route('solicitudes-subida.rechazar', $solicitud), [
                'comentario_revisor' => '',
            ])
            ->assertSessionHasErrors('comentario_revisor');
    }

    public function test_no_se_puede_aprobar_solicitud_ya_procesada(): void
    {
        Storage::fake('filecenter');

        ['empresa' => $empresa, 'admin' => $admin, 'empleado' => $empleado, 'carpeta' => $carpeta] = $this->crearEscenario();

        $solicitud = SolicitudSubida::factory()->create([
            'carpeta_id'     => $carpeta->id,
            'solicitante_id' => $empleado->id,
            'status'         => 'Rechazado',
        ]);

        $this->actingAs($admin)
            ->post(route('solicitudes-subida.aprobar', $solicitud))
            ->assertSessionHasErrors('error');
    }

    // ── Autorización ──────────────────────────────────────────

    public function test_empleado_no_puede_aprobar_solicitud(): void
    {
        ['empresa' => $empresa, 'empleado' => $empleado, 'carpeta' => $carpeta] = $this->crearEscenario();

        $solicitud = SolicitudSubida::factory()->create([
            'carpeta_id'     => $carpeta->id,
            'solicitante_id' => $empleado->id,
            'status'         => 'Pendiente',
        ]);

        $this->actingAs($empleado)
            ->post(route('solicitudes-subida.aprobar', $solicitud))
            ->assertForbidden();
    }

    public function test_admin_de_otra_empresa_no_puede_aprobar(): void
    {
        ['empleado' => $empleado, 'carpeta' => $carpeta] = $this->crearEscenario();

        $otraEmp  = Empresa::factory()->create();
        $adminAjeno = Usuario::factory()->create(['empresa_id' => $otraEmp->id, 'rol' => 'Admin']);

        $solicitud = SolicitudSubida::factory()->create([
            'carpeta_id'     => $carpeta->id,
            'solicitante_id' => $empleado->id,
            'status'         => 'Pendiente',
        ]);

        $this->actingAs($adminAjeno)
            ->post(route('solicitudes-subida.aprobar', $solicitud))
            ->assertForbidden();
    }

    // ── Modelo helpers ────────────────────────────────────────

    public function test_modelo_esta_pendiente_devuelve_correcto(): void
    {
        $sol = SolicitudSubida::factory()->make(['status' => 'Pendiente']);
        $this->assertTrue($sol->estaPendiente());

        $sol->status = 'Aprobado';
        $this->assertFalse($sol->estaPendiente());
    }

    public function test_modelo_tamanio_formateado(): void
    {
        $sol = SolicitudSubida::factory()->make(['tamanio_bytes' => 1048576]);
        $this->assertEquals('1 MB', $sol->tamanioFormateado());
    }
}
