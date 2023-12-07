<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Diretores;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


use Tests\TestCase;

class CasoDiretoresTest extends TestCase
{
    use RefreshDatabase, WithFaker;


    public function test_criar_diretores()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $dados = [
            'nome_diretor' => $this->faker->name,
        ];
        $this->post(route('diretores.store'), $dados);

        $this->assertCount(1, Diretores::all());

        $this->assertStringContainsString($dados['nome_diretor'], session('mensagem.sucesso'));
    }

    public function test_listar_diretores()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $diretores = Diretores::factory()->count(3)->create();

        $response = $this->get(route('diretores.list'));

        foreach ($diretores as $diretor) {
            $response->assertSee($diretor->nome);
        }

    }

    public function test_editar_diretor()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $diretor = Diretores::factory()->create();

        $novosDados = [
            'nome_diretor' => 'Novo Nome do Diretor',
        ];

        $this->put(route('diretores.update', ['id' => $diretor->id]), $novosDados);

        $this->assertDatabaseHas('diretores', [
            'id' => $diretor->id,
            'nome' => $novosDados['nome_diretor'],
        ]);
    }

    public function test_excluir_diretor()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $diretor = Diretores::factory()->create();

        $this->delete(route('diretores.destroy', ['id' => $diretor->id]));


        $this->assertDatabaseMissing('diretores', ['id' => $diretor->id]);
    }


}
