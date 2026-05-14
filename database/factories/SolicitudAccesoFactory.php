<?php

namespace Database\Factories;

use App\Models\Carpeta;
use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class SolicitudAccesoFactory extends Factory
{
    protected $model = \App\Models\SolicitudAcceso::class;

    public function definition(): array
    {
        return [
            'solicitante_id'     => Usuario::factory(),
            'empresa_objetivo_id'=> Empresa::factory(),
            'carpeta_id'         => Carpeta::factory(),
            'archivo_id'         => null,
            'razon'              => fake()->sentence(),
            'status'             => 'Pendiente',
            'revisado_por'       => null,
            'revisado_en'        => null,
            'comentario_revisor' => null,
            'tipo_acceso'        => 'Lectura',
            'caduca_en'          => null,
        ];
    }

    public function aprobada(): static
    {
        return $this->state([
            'status'     => 'Aprobado',
            'revisado_en'=> now(),
        ]);
    }

    public function rechazada(): static
    {
        return $this->state([
            'status'     => 'Rechazado',
            'revisado_en'=> now(),
        ]);
    }

    public function tipoLectura(): static
    {
        return $this->state(['tipo_acceso' => 'Lectura']);
    }

    public function tipoDescargar(): static
    {
        return $this->state(['tipo_acceso' => 'Descargar']);
    }

    public function vencida(): static
    {
        return $this->state([
            'status'   => 'Aprobado',
            'caduca_en'=> now()->subDay(),
        ]);
    }
}
