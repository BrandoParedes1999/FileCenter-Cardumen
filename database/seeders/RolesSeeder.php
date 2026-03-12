<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de Spatie antes de crear roles
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'Superadmin',   // Control total absoluto — corporativo
            'Aux_QHSE',     // Control total en archivos de TODAS las empresas
            'Admin',        // Gestión total dentro de su empresa
            'Gerente',      // Solo su departamento
            'Auxiliar',     // Solo carpetas asignadas
            'Empleado',     // Solo lectura por defecto
        ];

        foreach ($roles as $rol) {
            Role::firstOrCreate([
                'name'       => $rol,
                'guard_name' => 'web',
            ]);
        }

        // Asignar rol Superadmin al primer usuario (id=1)
        // $superadmin = \App\Models\Usuario::find(1);
        // if ($superadmin) {
        //     $superadmin->assignRole('Superadmin');
        // Asignar rol Superadmin al usuario id=1
        $superadmin = DB::table('usuarios')->where('id', 1)->first();
        if ($superadmin) {
            $rol = Role::where('name', 'Superadmin')->first();
            if ($rol) {
                DB::table('model_has_roles')->insert([
                    'role_id'    => $rol->id,
                    'model_type' => 'App\Models\Usuario',
                    'model_id'   => $superadmin->id,
                ]);
            }
        }
    }
}