<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;
use App\Models\Atores;
use App\Models\Diretores;
use App\Models\Filmes;

class ProcessMovie implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $movie;

    public function __construct($movie)
    {
        $this->movie = $movie;
    }

    public function handle()
    {
        $apiKey = 'bf1ddac8920d395547c13e1bad46874c';

        $movieId = $this->movie['id'];
        $creditsUrl = "https://api.themoviedb.org/3/movie/{$movieId}/credits?api_key={$apiKey}&language=pt-BR";
        $detailsUrl = "https://api.themoviedb.org/3/movie/{$movieId}?api_key={$apiKey}&language=pt-BR";

        $creditsResponse = Http::timeout(120)->get($creditsUrl);
        $detailsResponse = Http::timeout(120)->get($detailsUrl);

        if ($creditsResponse->successful() && $detailsResponse->successful()) {
            $creditsData = $creditsResponse->json();
            $movieDetails = $detailsResponse->json();

            // Resto do seu código para processar e salvar os filmes
            $diretor = collect($creditsData['crew'])->first(function ($member) {
                return $member['department'] === 'Directing';
            });

            if (isset($creditsData['cast'][0])) {
                $atorPrincipal = $creditsData['cast'][0];
            } else {
                $atorPrincipal = "Não encontrado";
            }

            $diretorNome = $diretor ? $diretor['name'] : 'Diretor desconhecido';
            $diretorModel = Diretores::firstOrNew(['nome' => $diretorNome]);
            $diretorModel->save();

            if (is_string($atorPrincipal)) {
                $atorPrincipalNome = $atorPrincipal;
            } else {
                $atorPrincipalNome = $atorPrincipal['name'];
            }

            $atorPrincipalModel = Atores::firstOrNew(['nome' => $atorPrincipalNome]);
            $atorPrincipalModel->save();

            $filme = new Filmes();
            $filme->nome = $movieDetails['title'];
            $filme->fk_diretor = $diretorModel->id;
            $filme->fk_ator_principal = $atorPrincipalModel->id;
            $filme->descricao = $movieDetails['overview'] ? $movieDetails['overview'] : "Não encontrada";
            if (!empty($movieDetails['genres']) && isset($movieDetails['genres'][0]['name'])) {
                $filme->categoria = $movieDetails['genres'][0]['name'];
            } else {
                $filme->categoria = "Gênero desconhecido";
            }
            $filme->urlimg = "https://image.tmdb.org/t/p/w500{$movieDetails['poster_path']}";
            $filme->save();
        }
    }
}
