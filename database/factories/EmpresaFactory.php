<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmpresaFactory extends Factory
{
    protected $model = \App\Models\Empresa::class;

    public function definition(): array
    {
        return [
            'nombre'          => fake()->company(),
            'siglas'          => strtoupper(fake()->lexify('???')),
            'logo'            => 'logo_default.png',
            'es_corporativo'  => false,
            'color_primario'  => '#1B3A6B',
            'color_secundario'=> '#2E5FA3',
            'color_terciario' => '#E8EEF7',
            'activo'          => true,
        ];
    }

    public function corporativa(): static
    {
        return $this->state(['es_corporativo' => true]);
    }

    public function inactiva(): static
    {
        return $this->state(['activo' => false]);
    }
}
