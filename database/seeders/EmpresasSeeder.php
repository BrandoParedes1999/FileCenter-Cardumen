<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpresasSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('empresas')->insert([
            [
                'nombre'           => 'Corporativo',
                'siglas'           => 'CORP',
                'logo'             => 'logo_corp.png',
                'es_corporativo'   => 1,
                'color_primario'   => '#1B3A6B',
                'color_secundario' => '#2E5FA3',
                'activo'           => 1,
            ],
            [
                'nombre'           => 'Empresa 1',
                'siglas'           => 'EMP1',
                'logo'             => 'logo_emp1.png',
                'es_corporativo'   => 0,
                'color_primario'   => '#2E5FA3',
                'color_secundario' => '#D6E4F7',
                'activo'           => 1,
            ],
            [
                'nombre'           => 'Empresa 2',
                'siglas'           => 'EMP2',
                'logo'             => 'logo_emp2.png',
                'es_corporativo'   => 0,
                'color_primario'   => '#1A6B3A',
                'color_secundario' => '#D6F7E4',
                'activo'           => 1,
            ],
            [
                'nombre'           => 'Empresa 3',
                'siglas'           => 'EMP3',
                'logo'             => 'logo_emp3.png',
                'es_corporativo'   => 0,
                'color_primario'   => '#B85C00',
                'color_secundario' => '#FFF0E0',
                'activo'           => 1,
            ],
            [
                'nombre'           => 'Empresa 4',
                'siglas'           => 'EMP4',
                'logo'             => 'logo_emp4.png',
                'es_corporativo'   => 0,
                'color_primario'   => '#5B2C8D',
                'color_secundario' => '#EEE6F7',
                'activo'           => 1,
            ],
        ]);
    }
}
