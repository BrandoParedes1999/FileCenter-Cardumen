<?php

namespace Database\Factories;

use App\Models\Carpeta;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class SolicitudSubidaFactory extends Factory
{
    protected $model = \App\Models\SolicitudSubida::class;

    public function definition(): array
    {
        $extension = fake()->randomElement(['pdf', 'docx', 'xlsx']);

        return [
            'carpeta_id'            => Carpeta::factory()->requiereAprobacion(),
            'solicitante_id'        => Usuario::factory()->empleado(),
            'nombre_original'       => fake()->word() . '.' . $extension,
            'nombre_almacenamiento' => fake()->uuid() . '.' . $extension,
            'ruta_temporal'         => 'temp/' . fake()->uuid() . '.' . $extension,
            'hash_sha256'           => fake()->sha256(),
            'tipo_mime'             => 'application/pdf',
            'extension'             => $extension,
            'tamanio_bytes'         => fake()->numberBetween(1024, 5242880),
            'descripcion'           => null,
            'status'                => 'Pendiente',
            'revisado_por'          => null,
            'revisado_en'           => null,
            'comentario_revisor'    => null,
            'archivo_id'            => null,
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
            'status'             => 'Rechazado',
            'revisado_en'        => now(),
            'comentario_revisor' => fake()->sentence(),
        ]);
    }
}
