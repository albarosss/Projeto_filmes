<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

use Tests\TestCase;

class CasoUsersTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;


    public function test_criar_novo_usuario()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'admin'=> 0,
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_listar_usuarios()
    {
        $users = User::factory()->count(3)->state(function (array $attributes) {
            return [
                'name' => Str::random(10),
                'email' => Str::random(10),
                'admin' => 1,
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ];
        })->create();
        $this->actingAs($users[0]);

        $response = $this->get('/users/list');

        $response->assertStatus(200);
        $response->assertSeeInOrder($users->pluck('name')->toArray());
    }

    public function test_editar_usuario()
    {
        $user = User::factory()->create();
        $user->refresh();

        $this->actingAs($user);

        $novosDados = [
            'name' => 'teste',
            'email' => 'teste@gmail.com',
        ];

        $this->put('/users/atualizar', $novosDados);

        $user->refresh();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $novosDados['email'],
            'name' => $novosDados['name'],
        ]);

    }

    public function test_excluir_usuario()
    {
        $user = User::factory()->create();

        $this->actingAs( User::factory()->count(3)->state(function (array $attributes) {
            return [
                'name' => Str::random(10),
                'email' => Str::random(10),
                'admin' => 1,
                'email_verified_at' => now(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token' => Str::random(10),
            ];
        })->create()[0]);

        $this->delete("/users/{$user->id}");

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }



}
