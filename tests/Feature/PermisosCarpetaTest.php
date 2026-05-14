<?php

namespace Tests\Feature;

use App\Models\Carpeta;
use App\Models\Empresa;
use App\Models\PermisoCarpeta;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermisosCarpetaTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ─────────────────────────────────────────────

    private function crearEmpresa(bool $corporativa = false): Empresa
    {
        return Empresa::factory()->create(['es_corporativo' => $corporativa]);
    }

    private function crearUsuario(Empresa $empresa, string $rol = 'Empleado'): Usuario
    {
        return Usuario::factory()->create([
            'empresa_id' => $empresa->id,
            'rol'        => $rol,
        ]);
    }

    private function crearCarpeta(Empresa $empresa, array $attrs = []): Carpeta
    {
        return Carpeta::factory()->create(array_merge(['empresa_id' => $empresa->id], $attrs));
    }

    // ── Acceso básico ────────────────────────────────────────

    public function test_carpeta_publica_es_legible_sin_permisos(): void
    {
        $empresa  = $this->crearEmpresa();
        $usuario  = $this->crearUsuario($empresa);
        $carpeta  = $this->crearCarpeta($empresa, ['es_publico' => true]);

        $this->assertTrue($carpeta->usuarioPuedeLeer($usuario));
    }

    public function test_carpeta_privada_no_es_legible_sin_permisos(): void
    {
        $empresa  = $this->crearEmpresa();
        $usuario  = $this->crearUsuario($empresa);
        $carpeta  = $this->crearCarpeta($empresa, ['es_publico' => false]);

        $this->assertFalse($carpeta->usuarioPuedeLeer($usuario));
    }

    public function test_admin_puede_leer_cualquier_carpeta_de_su_empresa(): void
    {
        $empresa  = $this->crearEmpresa();
        $admin    = $this->crearUsuario($empresa, 'Admin');
        $carpeta  = $this->crearCarpeta($empresa, ['es_publico' => false]);

        $this->assertTrue($carpeta->usuarioPuedeLeer($admin));
    }

    public function test_superadmin_puede_leer_cualquier_carpeta(): void
    {
        $empresaA = $this->crearEmpresa();
        $empresaB = $this->crearEmpresa();
        $superadmin = $this->crearUsuario($empresaA, 'Superadmin');
        $carpeta    = $this->crearCarpeta($empresaB, ['es_publico' => false]);

        $this->assertTrue($carpeta->usuarioPuedeLeer($superadmin));
    }

    // ── Permisos explícitos ──────────────────────────────────

    public function test_permiso_individual_permite_leer_carpeta_privada(): void
    {
        $empresa = $this->crearEmpresa();
        $usuario = $this->crearUsuario($empresa);
        $carpeta = $this->crearCarpeta($empresa, ['es_publico' => false]);

        PermisoCarpeta::factory()->create([
            'carpeta_id'  => $carpeta->id,
            'usuario_id'  => $usuario->id,
            'puede_leer'  => true,
        ]);

        $this->assertTrue($carpeta->usuarioPuedeLeer($usuario));
    }

    public function test_permiso_por_rol_permite_leer_carpeta(): void
    {
        $empresa = $this->crearEmpresa();
        $usuario = $this->crearUsuario($empresa, 'Auxiliar');
        $carpeta = $this->crearCarpeta($empresa, ['es_publico' => false]);

        PermisoCarpeta::factory()->create([
            'carpeta_id' => $carpeta->id,
            'empresa_id' => $empresa->id,
            'rol'        => 'Auxiliar',
            'puede_leer' => true,
        ]);

        $this->assertTrue($carpeta->usuarioPuedeLeer($usuario));
    }

    // ── Herencia de permisos ─────────────────────────────────

    public function test_permiso_heredado_del_padre_permite_leer_hijo(): void
    {
        $empresa = $this->crearEmpresa();
        $usuario = $this->crearUsuario($empresa);
        $padre   = $this->crearCarpeta($empresa, ['es_publico' => false]);
        $hijo    = Carpeta::factory()->create([
            'empresa_id' => $empresa->id,
            'padre_id'   => $padre->id,
            'es_publico' => false,
        ]);

        PermisoCarpeta::factory()->create([
            'carpeta_id'  => $padre->id,
            'usuario_id'  => $usuario->id,
            'puede_leer'  => true,
            'heredar'     => true,
        ]);

        $this->assertTrue($hijo->usuarioPuedeLeer($usuario));
    }

    public function test_permiso_no_heredado_no_se_propaga_al_hijo(): void
    {
        $empresa = $this->crearEmpresa();
        $usuario = $this->crearUsuario($empresa);
        $padre   = $this->crearCarpeta($empresa, ['es_publico' => false]);
        $hijo    = Carpeta::factory()->create([
            'empresa_id' => $empresa->id,
            'padre_id'   => $padre->id,
            'es_publico' => false,
        ]);

        PermisoCarpeta::factory()->create([
            'carpeta_id'  => $padre->id,
            'usuario_id'  => $usuario->id,
            'puede_leer'  => true,
            'heredar'     => false,
        ]);

        $this->assertFalse($hijo->usuarioPuedeLeer($usuario));
    }

    // ── Descarga ─────────────────────────────────────────────

    public function test_carpeta_privada_normal_sin_permiso_no_puede_descargar(): void
    {
        $empresa = $this->crearEmpresa();
        $usuario = $this->crearUsuario($empresa);
        $carpeta = $this->crearCarpeta($empresa, [
            'es_publico'  => false,
            'modo_acceso' => 'normal',
        ]);

        // Sin permiso explícito: el modelo devuelve false
        $this->assertFalse($carpeta->usuarioPuedeDescargar($usuario));
    }

    public function test_carpeta_solo_lectura_bloquea_descarga_sin_permiso(): void
    {
        $empresa = $this->crearEmpresa();
        $usuario = $this->crearUsuario($empresa);
        $carpeta = $this->crearCarpeta($empresa, [
            'es_publico'  => true,
            'modo_acceso' => 'solo_lectura',
        ]);

        $this->assertFalse($carpeta->usuarioPuedeDescargar($usuario));
    }

    public function test_permiso_explicito_sin_descarga_bloquea_en_carpeta_publica(): void
    {
        $empresa = $this->crearEmpresa();
        $usuario = $this->crearUsuario($empresa);
        $carpeta = $this->crearCarpeta($empresa, [
            'es_publico'  => true,
            'modo_acceso' => 'normal',
        ]);

        PermisoCarpeta::factory()->create([
            'carpeta_id'      => $carpeta->id,
            'usuario_id'      => $usuario->id,
            'puede_leer'      => true,
            'puede_descargar' => false,
        ]);

        $this->assertFalse($carpeta->usuarioPuedeDescargar($usuario));
    }

    // ── Corporativo ──────────────────────────────────────────

    public function test_carpeta_corporativa_es_legible_por_todos(): void
    {
        $corp    = $this->crearEmpresa(true);
        $otraEmp = $this->crearEmpresa();
        $usuario = $this->crearUsuario($otraEmp);
        $carpeta = $this->crearCarpeta($corp, ['es_publico' => false]);

        $carpeta->load('empresa');

        $this->assertTrue($carpeta->usuarioPuedeLeer($usuario));
    }

    public function test_carpeta_corporativa_modo_normal_permite_descargar(): void
    {
        $corp    = $this->crearEmpresa(true);
        $otraEmp = $this->crearEmpresa();
        $usuario = $this->crearUsuario($otraEmp);
        $carpeta = $this->crearCarpeta($corp, [
            'es_publico'  => false,
            'modo_acceso' => 'normal',
        ]);

        $carpeta->load('empresa');

        $this->assertTrue($carpeta->usuarioPuedeDescargar($usuario));
    }

    // ── Subir / Editar / Borrar ───────────────────────────────

    public function test_sin_permiso_no_puede_subir(): void
    {
        $empresa = $this->crearEmpresa();
        $usuario = $this->crearUsuario($empresa);
        $carpeta = $this->crearCarpeta($empresa);

        $this->assertFalse($carpeta->usuarioPuedeSubir($usuario));
    }

    public function test_con_permiso_puede_subir(): void
    {
        $empresa = $this->crearEmpresa();
        $usuario = $this->crearUsuario($empresa);
        $carpeta = $this->crearCarpeta($empresa);

        PermisoCarpeta::factory()->create([
            'carpeta_id'  => $carpeta->id,
            'usuario_id'  => $usuario->id,
            'puede_subir' => true,
        ]);

        $this->assertTrue($carpeta->usuarioPuedeSubir($usuario));
    }
}
