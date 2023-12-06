<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Atores;
use App\Models\Diretores;
use App\Models\Filmes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CasoFilmesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_criar_filme()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Atores::factory()->create(['id' => 1, 'nome' => 'Ator Teste']);
        Diretores::factory()->create(['id' => 1, 'nome' => 'Diretor Teste']);

        $response = $this->post('/filmes', [
            'nome' => 'Meu Filme Teste',
            'descricao' => 'Descrição do meu filme teste.',
            'categoria' => 'Ação',
            'urlimg' => 'teste',
            'fk_ator_principal' => '1',
            'fk_diretor' => '1',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('filmes', ['nome' => 'Meu Filme Teste']);
    }

    public function test_listar_filmes()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Atores::factory()->create(['id' => 1, 'nome' => 'Ator Teste']);
        Diretores::factory()->create(['id' => 1, 'nome' => 'Diretor Teste']);
        Filmes::factory()->create(['nome' => 'Filme 1']);
        Filmes::factory()->create(['nome' => 'Filme 2']);

        $response = $this->get('/filmes');

        $response->assertSee('Filme 1');
        $response->assertSee('Filme 2');
    }



    public function test_editar_filme()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Atores::factory()->create(['id' => 1, 'nome' => 'Ator Teste']);
        Diretores::factory()->create(['id' => 1, 'nome' => 'Diretor Teste']);

        $filme = Filmes::factory()->create([
            'fk_ator_principal' => 1,
            'fk_diretor' => 1,
        ]);

        $response = $this->get("/filmes/{$filme->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('filmes.edit');
        $response->assertViewHas('filme');
    }

    public function test_excluir_filme()
    {

        $user = User::factory()->create();
        $this->actingAs($user);

        Atores::factory()->create(['id' => 1, 'nome' => 'Ator Teste']);
        Diretores::factory()->create(['id' => 1, 'nome' => 'Diretor Teste']);
        $filme = Filmes::factory()->create(['nome' => 'Filme 1']);

        $this->delete("/filmes/{$filme->id}");

        $this->assertDatabaseMissing('filmes', ['id' => $filme->id]);


    }

}
