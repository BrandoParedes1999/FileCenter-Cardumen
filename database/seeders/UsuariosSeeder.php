<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('SUPERADMIN_PASSWORD');

        if (empty($password)) {
            throw new \RuntimeException('La variable de entorno SUPERADMIN_PASSWORD es obligatoria antes de ejecutar el seeder.');
        }

        DB::table('usuarios')->insert([
            'empresa_id'  => 1,
            'nombre'      => 'Super',
            'paterno'     => 'Admin',
            'materno'     => 'Sistema',
            'email'       => 'superadmin@filecenter.com',
            'password'    => Hash::make($password),
            'rol'         => 'Superadmin',
            'es_activo'   => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
}
