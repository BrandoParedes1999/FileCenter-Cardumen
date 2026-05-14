<?php

namespace Database\Factories;

use App\Models\Carpeta;
use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermisoCarpetaFactory extends Factory
{
    protected $model = \App\Models\PermisoCarpeta::class;

    public function definition(): array
    {
        return [
            'carpeta_id'      => Carpeta::factory(),
            'usuario_id'      => null,
            'empresa_id'      => null,
            'rol'             => null,
            'puede_leer'      => true,
            'puede_subir'     => false,
            'puede_editar'    => false,
            'puede_borrar'    => false,
            'puede_descargar' => false,
            'heredar'         => true,
            'concedido_por'   => Usuario::factory(),
        ];
    }

    public function paraUsuario(Usuario $usuario): static
    {
        return $this->state(['usuario_id' => $usuario->id]);
    }

    public function paraRol(string $rol, ?Empresa $empresa = null): static
    {
        return $this->state([
            'rol'        => $rol,
            'empresa_id' => $empresa?->id,
        ]);
    }

    public function accesoCompleto(): static
    {
        return $this->state([
            'puede_leer'      => true,
            'puede_subir'     => true,
            'puede_editar'    => true,
            'puede_borrar'    => true,
            'puede_descargar' => true,
        ]);
    }

    public function soloLectura(): static
    {
        return $this->state([
            'puede_leer'      => true,
            'puede_subir'     => false,
            'puede_editar'    => false,
            'puede_borrar'    => false,
            'puede_descargar' => true,
        ]);
    }

    public function sinDescarga(): static
    {
        return $this->state(['puede_descargar' => false]);
    }
}
