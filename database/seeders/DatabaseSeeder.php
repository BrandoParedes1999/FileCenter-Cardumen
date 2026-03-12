<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    //SIRVE PARA SUBIR LOS CONTENIDOS A LAS TABLAS VACIAS
    /**
     * Orden obligatorio — respetar dependencias FK:
     *
     *  1. empresas           (sin dependencias)
     *  2. usuarios           (depende: empresas)
     *  3. roles de Spatie    (depende: usuarios)
     *  4. carpetas           (depende: empresas, usuarios)
     */
    public function run(): void
    {
        $this->call([
            EmpresasSeeder::class,
            UsuariosSeeder::class,
            RolesSeeder::class,
            //CarpetasSeeder::class,
        ]);
    }
}
