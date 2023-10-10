<?php

namespace App\Http\Controllers;

use App\Models\Atores;
use App\Repositories\AtoresRepository;
use App\Http\Requests\AtoresFormRequest;


class AtoresController
{
    public function __construct(private AtoresRepository $repository)
    {
    }

    public function store(AtoresFormRequest $request)
    {
        $ator = new Atores();
        $ator->nome = $request->input('nome_ator');
        $ator->idade = $request->input('idade');
        $ator->save();

        return to_route('filmes.create')
        ->with('mensagem.sucesso', "ator '{$ator->nome}' adicionado com sucesso");
    }
}
