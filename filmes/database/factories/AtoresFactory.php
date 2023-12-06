<?php

namespace Database\Factories;

use App\Models\Atores;
use Illuminate\Database\Eloquent\Factories\Factory;

class AtoresFactory extends Factory
{
    protected $model = Atores::class;

    public function definition()
    {
        return [
            'nome' => $this->faker->name,
        ];
    }
}
