<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        // ⚠ Cambia la contraseña antes de desplegar en producción
        DB::table('usuarios')->insert([
            'empresa_id'  => 1,          // Corporativo
            'nombre'      => 'Super',
            'paterno'     => 'Admin',
            'materno'     => 'Sistema',
            'email'       => 'superadmin@filecenter.com',
            'password'    => Hash::make('Superadmin2026!'),
            'rol'         => 'Superadmin',
            'es_activo'   => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
}
