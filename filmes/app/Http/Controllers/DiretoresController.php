<?php

namespace App\Http\Controllers;

use App\Models\Diretores;
use Illuminate\Http\Request;
use App\Repositories\DiretoresRepository;
use App\Http\Requests\DiretoresFormRequest;


class DiretoresController
{
    public function __construct(private DiretoresRepository $repository)
    {
    }

    public function store(DiretoresFormRequest $request)
    {
        $diretor = new Diretores();
        $diretor->nome = $request->input('nome diretor');
        $diretor->save();

        return to_route('filmes.create')
        ->with('mensagem.sucesso', "Diretor '{$diretor->nome}' adicionado com sucesso");
    }
}
