<?php

namespace Tests\Feature;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\Empresa;
use App\Models\PermisoCarpeta;
use App\Models\SolicitudAcceso;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArchivoAccessTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ─────────────────────────────────────────────

    private function setup_basico(): array
    {
        $empresa = Empresa::factory()->create(['es_corporativo' => false]);
        $admin   = Usuario::factory()->create(['empresa_id' => $empresa->id, 'rol' => 'Admin']);
        $carpeta = Carpeta::factory()->create([
            'empresa_id'  => $empresa->id,
            'es_publico'  => false,
            'modo_acceso' => 'normal',
        ]);
        $archivo = Archivo::factory()->create([
            'carpeta_id' => $carpeta->id,
            'subido_por' => $admin->id,
        ]);

        return compact('empresa', 'admin', 'carpeta', 'archivo');
    }

    // ── Descarga: ArchivoPolicy ──────────────────────────────

    public function test_superadmin_puede_descargar_cualquier_archivo(): void
    {
        \Illuminate\Support\Facades\Storage::fake('filecenter');

        ['archivo' => $archivo] = $this->setup_basico();
        \Illuminate\Support\Facades\Storage::disk('filecenter')->put($archivo->ruta_disco, 'contenido');

        $empresaB   = Empresa::factory()->create();
        $superadmin = Usuario::factory()->create(['empresa_id' => $empresaB->id, 'rol' => 'Superadmin']);

        $this->actingAs($superadmin)
            ->get(route('archivos.descargar', $archivo))
            ->assertStatus(200);
    }

    public function test_empleado_sin_permiso_no_puede_descargar_archivo_privado(): void
    {
        ['empresa' => $empresa, 'carpeta' => $carpeta, 'archivo' => $archivo] = $this->setup_basico();

        $empleado = Usuario::factory()->create(['empresa_id' => $empresa->id, 'rol' => 'Empleado']);

        $this->actingAs($empleado)
            ->get(route('archivos.descargar', $archivo))
            ->assertForbidden();
    }

    public function test_empleado_con_permiso_descarga_puede_descargar(): void
    {
        ['empresa' => $empresa, 'carpeta' => $carpeta, 'archivo' => $archivo, 'admin' => $admin] = $this->setup_basico();

        $empleado = Usuario::factory()->create(['empresa_id' => $empresa->id, 'rol' => 'Empleado']);

        PermisoCarpeta::factory()->create([
            'carpeta_id'      => $carpeta->id,
            'usuario_id'      => $empleado->id,
            'puede_leer'      => true,
            'puede_descargar' => true,
            'concedido_por'   => $admin->id,
        ]);

        // El archivo existe en disco (mock)
        \Illuminate\Support\Facades\Storage::fake('filecenter');
        \Illuminate\Support\Facades\Storage::disk('filecenter')->put($archivo->ruta_disco, 'contenido de prueba');

        $this->actingAs($empleado)
            ->get(route('archivos.descargar', $archivo))
            ->assertStatus(200);
    }

    public function test_permiso_explicito_sin_descarga_bloquea_aunque_carpeta_sea_publica(): void
    {
        $empresa = Empresa::factory()->create(['es_corporativo' => false]);
        $admin   = Usuario::factory()->create(['empresa_id' => $empresa->id, 'rol' => 'Admin']);
        $carpeta = Carpeta::factory()->create([
            'empresa_id'  => $empresa->id,
            'es_publico'  => true,
            'modo_acceso' => 'normal',
        ]);
        $archivo  = Archivo::factory()->create(['carpeta_id' => $carpeta->id, 'subido_por' => $admin->id]);
        $empleado = Usuario::factory()->create(['empresa_id' => $empresa->id, 'rol' => 'Empleado']);

        // Permiso explícito que DENIEGA descarga
        PermisoCarpeta::factory()->create([
            'carpeta_id'      => $carpeta->id,
            'usuario_id'      => $empleado->id,
            'puede_leer'      => true,
            'puede_descargar' => false,
        ]);

        $this->actingAs($empleado)
            ->get(route('archivos.descargar', $archivo))
            ->assertForbidden();
    }

    // ── Cross-empresa ────────────────────────────────────────

    public function test_usuario_otra_empresa_no_puede_ver_archivo_sin_solicitud(): void
    {
        ['archivo' => $archivo] = $this->setup_basico();
        $empresaB = Empresa::factory()->create();
        $externo  = Usuario::factory()->create(['empresa_id' => $empresaB->id, 'rol' => 'Empleado']);

        $this->actingAs($externo)
            ->get(route('archivos.ver', $archivo))
            ->assertForbidden();
    }

    public function test_usuario_otra_empresa_puede_descargar_con_solicitud_aprobada_descargar(): void
    {
        ['empresa' => $empresa, 'carpeta' => $carpeta, 'archivo' => $archivo] = $this->setup_basico();

        $empresaB = Empresa::factory()->create();
        $externo  = Usuario::factory()->create(['empresa_id' => $empresaB->id, 'rol' => 'Empleado']);

        SolicitudAcceso::factory()->create([
            'solicitante_id'      => $externo->id,
            'empresa_objetivo_id' => $empresa->id,
            'carpeta_id'          => $carpeta->id,
            'archivo_id'          => $archivo->id,
            'status'              => 'Aprobado',
            'tipo_acceso'         => 'Descargar',
            'caduca_en'           => null,
        ]);

        \Illuminate\Support\Facades\Storage::fake('filecenter');
        \Illuminate\Support\Facades\Storage::disk('filecenter')->put($archivo->ruta_disco, 'contenido');

        $this->actingAs($externo)
            ->get(route('archivos.descargar', $archivo))
            ->assertStatus(200);
    }

    public function test_solicitud_vencida_no_permite_descarga(): void
    {
        ['empresa' => $empresa, 'carpeta' => $carpeta, 'archivo' => $archivo] = $this->setup_basico();

        $empresaB = Empresa::factory()->create();
        $externo  = Usuario::factory()->create(['empresa_id' => $empresaB->id, 'rol' => 'Empleado']);

        SolicitudAcceso::factory()->create([
            'solicitante_id'      => $externo->id,
            'empresa_objetivo_id' => $empresa->id,
            'carpeta_id'          => $carpeta->id,
            'archivo_id'          => $archivo->id,
            'status'              => 'Aprobado',
            'tipo_acceso'         => 'Descargar',
            'caduca_en'           => now()->subDay(),
        ]);

        $this->actingAs($externo)
            ->get(route('archivos.descargar', $archivo))
            ->assertForbidden();
    }

    // ── Edición ──────────────────────────────────────────────

    public function test_admin_puede_editar_archivo_de_su_empresa(): void
    {
        ['empresa' => $empresa, 'admin' => $admin, 'archivo' => $archivo] = $this->setup_basico();

        $this->actingAs($admin)
            ->patch(route('archivos.update', $archivo), [
                'descripcion' => 'nueva descripción',
            ])
            ->assertRedirect();
    }

    public function test_empleado_sin_permiso_no_puede_editar_archivo(): void
    {
        ['empresa' => $empresa, 'archivo' => $archivo] = $this->setup_basico();

        $empleado = Usuario::factory()->create(['empresa_id' => $empresa->id, 'rol' => 'Empleado']);

        $this->actingAs($empleado)
            ->patch(route('archivos.update', $archivo), [
                'descripcion' => 'nueva descripción',
            ])
            ->assertForbidden();
    }

    // ── Eliminación ──────────────────────────────────────────

    public function test_admin_puede_eliminar_archivo_de_su_empresa(): void
    {
        ['admin' => $admin, 'archivo' => $archivo] = $this->setup_basico();

        $this->actingAs($admin)
            ->delete(route('archivos.destroy', $archivo))
            ->assertRedirect();

        $this->assertSoftDeleted('archivos', ['id' => $archivo->id]);
    }

    public function test_usuario_otra_empresa_no_puede_eliminar_archivo(): void
    {
        ['archivo' => $archivo] = $this->setup_basico();

        $empresaB = Empresa::factory()->create();
        $adminB   = Usuario::factory()->create(['empresa_id' => $empresaB->id, 'rol' => 'Admin']);

        $this->actingAs($adminB)
            ->delete(route('archivos.destroy', $archivo))
            ->assertForbidden();
    }
}
