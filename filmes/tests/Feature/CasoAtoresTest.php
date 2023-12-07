<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Atores;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CasoAtoresTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_criar_atores()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $dados = [
            'nome_ator' => $this->faker->name,
        ];

        // Act
        $this->post(route('atores.store'), $dados);

        // Assert
        $this->assertCount(1, Atores::all());
        $this->assertStringContainsString($dados['nome_ator'], session('mensagem.sucesso'));
    }

    public function test_listar_atores()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $atores = Atores::factory()->count(3)->create();
        $response = $this->get(route('atores.list'));

        foreach ($atores as $ator) {
            $response->assertSee($ator->nome);
        }
    }

    public function test_editar_ator()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ator = Atores::factory()->create();

        $novosDados = [
            'nome_ator' => 'Novo Nome do Ator',
        ];

        $this->put(route('atores.update', ['id' => $ator->id]), $novosDados);

        $this->assertDatabaseHas('atores', [
            'id' => $ator->id,
            'nome' => $novosDados['nome_ator'],
        ]);
    }

    public function test_excluir_ator()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ator = Atores::factory()->create();

        $this->delete(route('atores.destroy', ['id' => $ator->id]));

        $this->assertDatabaseMissing('atores', ['id' => $ator->id]);
    }
}
