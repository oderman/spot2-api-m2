<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Zone>
 */
class ZoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'call_numero' => $this->fake()->string(10),
            'codigo_postal' => $this->fake()->number(),
            'colonia_predio' => $this->fake()->string(10),
            'superficie_terreno' => $this->fake()->number(10),
            'superficie_construccion' => $this->fake()->number(10),
            'uso_construccion' => $this->fake()->sentence(),
            'clave_rango_nivel' => $this->fake()->sentence(),
            'anio_construccion' => $this->fake()->sentence(),
            'instalaciones_especiales' => $this->fake()->sentence(),
            'valor_unitario_suelo' => $this->fake()->number(10),
            'valor_suelo' => $this->fake()->number(10),
            'clave_valor_unitario_suelo' => $this->fake()->sentence(),
            'colonia_cumpliemiento' => $this->fake()->sentence(),
            'alcaldia_cumplimiento' => $this->fake()->sentence(),
            'subsidio' => $this->fake()->number(10),
        ];
    }
}
