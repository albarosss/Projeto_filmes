<?php

use App\Models\Comentarios;
use App\Models\User;
use App\Models\Atores;
use App\Models\Diretores;
use App\Models\Filmes;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class CasoComentarioTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_adicionar_comentario_filme()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Atores::factory()->create(['id' => 1, 'nome' => 'Ator Teste']);
        Diretores::factory()->create(['id' => 1, 'nome' => 'Diretor Teste']);
        $filme = Filmes::factory()->create(['nome' => 'Filme 1']);

        $this->post("/filmes/{$filme->id}/comentar", [
            'comentario' => 'Este é um ótimo filme!',
            'avaliacao' => 5,
        ]);

        $this->assertDatabaseHas('comentarios', [
            'filme_id' => $filme->id,
            'usuario_id' => $user->id,
            'comentario' => 'Este é um ótimo filme!',
            'avaliacao' => 5,
        ]);
    }

    public function test_adicionar_comentario_sem_autenticacao()
    {
        Atores::factory()->create(['id' => 1, 'nome' => 'Ator Teste']);
        Diretores::factory()->create(['id' => 1, 'nome' => 'Diretor Teste']);
        $filme = Filmes::factory()->create();

        $response = $this->post("/filmes/{$filme->id}/comentar", [
            'comentario' => 'Este é um ótimo filme!',
            'avaliacao' => 5,
        ]);

        $response->assertRedirect('/login');
    }

}
