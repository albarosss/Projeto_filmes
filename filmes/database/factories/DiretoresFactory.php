<?php

namespace Database\Factories;

use App\Models\Diretores;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiretoresFactory extends Factory
{
    protected $model = Diretores::class;

    public function definition()
    {
        return [
            'nome' => $this->faker->name,
            // outras colunas, se houver...
        ];
    }
}
