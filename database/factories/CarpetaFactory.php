<?php

namespace Database\Factories;

use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarpetaFactory extends Factory
{
    protected $model = \App\Models\Carpeta::class;

    public function definition(): array
    {
        $empresa = Empresa::factory()->create();

        return [
            'empresa_id'                  => $empresa->id,
            'padre_id'                    => null,
            'nombre'                      => fake()->words(2, true),
            'path'                        => '/',
            'es_publico'                  => false,
            'modo_acceso'                 => 'normal',
            'requiere_aprobacion_subida'  => false,
            'creado_por'                  => Usuario::factory()->create(['empresa_id' => $empresa->id])->id,
        ];
    }

    public function publica(): static
    {
        return $this->state(['es_publico' => true]);
    }

    public function soloLectura(): static
    {
        return $this->state(['modo_acceso' => 'solo_lectura']);
    }

    public function conDescarga(): static
    {
        return $this->state(['modo_acceso' => 'con_descarga']);
    }

    public function requiereAprobacion(): static
    {
        return $this->state(['requiere_aprobacion_subida' => true]);
    }

    public function deEmpresa(Empresa $empresa): static
    {
        return $this->state(['empresa_id' => $empresa->id]);
    }

    public function creadaPor(Usuario $usuario): static
    {
        return $this->state(['creado_por' => $usuario->id]);
    }

    public function hija(\App\Models\Carpeta $padre): static
    {
        return $this->state([
            'padre_id'   => $padre->id,
            'empresa_id' => $padre->empresa_id,
        ]);
    }
}
