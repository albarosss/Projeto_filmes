<?php
namespace Database\Factories;

use App\Models\Filmes;
use Illuminate\Database\Eloquent\Factories\Factory;

class FilmesFactory extends Factory
{
    protected $model = Filmes::class;

    public function definition()
    {
        return [
            'nome' => 'Meu Filme Teste',
            'descricao' => 'Meu Filme Teste',
            'categoria' => 'Ação',
            'urlimg' => $this->faker->imageUrl,
            'fk_ator_principal' => 1,
            'fk_diretor' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
