<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarpetasSeeder extends Seeder
{
    /**
     * Crea la estructura inicial de carpetas del sistema.
     *
     * Estructura:
     *
     * Corporativo (empresa_id=1)
     *   └── Políticas y Procedimientos     [pública, solo_lectura]
     *   └── Normativas                     [pública, con_descarga]
     *   └── Comunicados Generales          [pública, normal]
     *
     * Empresa 1 (empresa_id=2)
     *   └── Documentos Generales           [privada, normal]
     *       └── Contratos                  [privada, normal, requiere_aprobacion]
     *       └── Reportes                   [privada, normal]
     *   └── Recursos Humanos               [privada, solo_lectura]
     *       └── Expedientes                [privada, solo_lectura]
     *
     * Empresa 2 (empresa_id=3)
     *   └── Documentos Generales           [privada, normal]
     *   └── Proyectos                      [privada, normal]
     *
     * Empresa 3 (empresa_id=4)
     *   └── Documentos Generales           [privada, normal]
     *
     * Empresa 4 (empresa_id=5)
     *   └── Documentos Generales           [privada, normal]
     */
    public function run(): void
    {
        $superadminId = DB::table('usuarios')->where('rol', 'Superadmin')->value('id') ?? 1;
        $now          = now();

        // ── Corporativo (empresa_id = 1) ─────────────────────────────────────

        $corpPoliticas = DB::table('carpetas')->insertGetId([
            'empresa_id'                 => 1,
            'padre_id'                   => null,
            'nombre'                     => 'Políticas y Procedimientos',
            'path'                       => '/corporativo/politicas',
            'es_publico'                 => 1,
            'modo_acceso'                => 'solo_lectura',
            'requiere_aprobacion_subida' => 0,
            'creado_por'                 => $superadminId,
            'created_at'                 => $now,
            'updated_at'                 => $now,
        ]);

        DB::table('carpetas')->insertGetId([
            'empresa_id'                 => 1,
            'padre_id'                   => null,
            'nombre'                     => 'Normativas',
            'path'                       => '/corporativo/normativas',
            'es_publico'                 => 1,
            'modo_acceso'                => 'con_descarga',
            'requiere_aprobacion_subida' => 0,
            'creado_por'                 => $superadminId,
            'created_at'                 => $now,
            'updated_at'                 => $now,
        ]);

        DB::table('carpetas')->insertGetId([
            'empresa_id'                 => 1,
            'padre_id'                   => null,
            'nombre'                     => 'Comunicados Generales',
            'path'                       => '/corporativo/comunicados',
            'es_publico'                 => 1,
            'modo_acceso'                => 'normal',
            'requiere_aprobacion_subida' => 0,
            'creado_por'                 => $superadminId,
            'created_at'                 => $now,
            'updated_at'                 => $now,
        ]);

        // ── Empresa 1 (empresa_id = 2) ────────────────────────────────────────

        $emp1Docs = DB::table('carpetas')->insertGetId([
            'empresa_id'                 => 2,
            'padre_id'                   => null,
            'nombre'                     => 'Documentos Generales',
            'path'                       => '/empresa1/documentos',
            'es_publico'                 => 0,
            'modo_acceso'                => 'normal',
            'requiere_aprobacion_subida' => 0,
            'creado_por'                 => $superadminId,
            'created_at'                 => $now,
            'updated_at'                 => $now,
        ]);

        DB::table('carpetas')->insertGetId([
            'empresa_id'                 => 2,
            'padre_id'                   => $emp1Docs,
            'nombre'                     => 'Contratos',
            'path'                       => '/empresa1/documentos/contratos',
            'es_publico'                 => 0,
            'modo_acceso'                => 'normal',
            'requiere_aprobacion_subida' => 1,
            'creado_por'                 => $superadminId,
            'created_at'                 => $now,
            'updated_at'                 => $now,
        ]);

        DB::table('carpetas')->insertGetId([
            'empresa_id'                 => 2,
            'padre_id'                   => $emp1Docs,
            'nombre'                     => 'Reportes',
            'path'                       => '/empresa1/documentos/reportes',
            'es_publico'                 => 0,
            'modo_acceso'                => 'normal',
            'requiere_aprobacion_subida' => 0,
            'creado_por'                 => $superadminId,
            'created_at'                 => $now,
            'updated_at'                 => $now,
        ]);

        $emp1RR = DB::table('carpetas')->insertGetId([
            'empresa_id'                 => 2,
            'padre_id'                   => null,
            'nombre'                     => 'Recursos Humanos',
            'path'                       => '/empresa1/rrhh',
            'es_publico'                 => 0,
            'modo_acceso'                => 'solo_lectura',
            'requiere_aprobacion_subida' => 0,
            'creado_por'                 => $superadminId,
            'created_at'                 => $now,
            'updated_at'                 => $now,
        ]);

        DB::table('carpetas')->insertGetId([
            'empresa_id'                 => 2,
            'padre_id'                   => $emp1RR,
            'nombre'                     => 'Expedientes',
            'path'                       => '/empresa1/rrhh/expedientes',
            'es_publico'                 => 0,
            'modo_acceso'                => 'solo_lectura',
            'requiere_aprobacion_subida' => 0,
            'creado_por'                 => $superadminId,
            'created_at'                 => $now,
            'updated_at'                 => $now,
        ]);

        // ── Empresa 2 (empresa_id = 3) ────────────────────────────────────────

        DB::table('carpetas')->insert([
            [
                'empresa_id'                 => 3,
                'padre_id'                   => null,
                'nombre'                     => 'Documentos Generales',
                'path'                       => '/empresa2/documentos',
                'es_publico'                 => 0,
                'modo_acceso'                => 'normal',
                'requiere_aprobacion_subida' => 0,
                'creado_por'                 => $superadminId,
                'created_at'                 => $now,
                'updated_at'                 => $now,
            ],
            [
                'empresa_id'                 => 3,
                'padre_id'                   => null,
                'nombre'                     => 'Proyectos',
                'path'                       => '/empresa2/proyectos',
                'es_publico'                 => 0,
                'modo_acceso'                => 'normal',
                'requiere_aprobacion_subida' => 0,
                'creado_por'                 => $superadminId,
                'created_at'                 => $now,
                'updated_at'                 => $now,
            ],
        ]);

        // ── Empresa 3 (empresa_id = 4) ────────────────────────────────────────

        DB::table('carpetas')->insert([
            [
                'empresa_id'                 => 4,
                'padre_id'                   => null,
                'nombre'                     => 'Documentos Generales',
                'path'                       => '/empresa3/documentos',
                'es_publico'                 => 0,
                'modo_acceso'                => 'normal',
                'requiere_aprobacion_subida' => 0,
                'creado_por'                 => $superadminId,
                'created_at'                 => $now,
                'updated_at'                 => $now,
            ],
        ]);

        // ── Empresa 4 (empresa_id = 5) ────────────────────────────────────────

        DB::table('carpetas')->insert([
            [
                'empresa_id'                 => 5,
                'padre_id'                   => null,
                'nombre'                     => 'Documentos Generales',
                'path'                       => '/empresa4/documentos',
                'es_publico'                 => 0,
                'modo_acceso'                => 'normal',
                'requiere_aprobacion_subida' => 0,
                'creado_por'                 => $superadminId,
                'created_at'                 => $now,
                'updated_at'                 => $now,
            ],
        ]);
    }
}
