<?php

namespace App\Http\Controllers;

use App\Models\Diretores;
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
        $diretor->nome = $request->input('nome_diretor');
        $diretor->save();

        return to_route('filmes.create')
        ->with('mensagem.sucesso', "Diretor '{$diretor->nome}' adicionado com sucesso");
    }

    public function list()
    {
        $diretores = Diretores::all();
        return view('diretores.list', compact('diretores'));
    }

    public function edit($id)
    {
        $diretor = Diretores::findOrFail($id);
        return view('diretores.edit', compact('diretor'));
    }

    public function update(DiretoresFormRequest $request, $id)
    {
        $diretor = Diretores::findOrFail($id);
        $diretor->nome = $request->input('nome_diretor');
        $diretor->save();

        return redirect()->route('diretores.list')->with('mensagem.sucesso', "Diretor '{$diretor->nome}' atualizado com sucesso");
    }

    public function destroy($id)
    {
        $diretor = Diretores::findOrFail($id);
        $diretor->delete();

        return redirect()->route('diretores.list')->with('mensagem.sucesso', "Diretor '{$diretor->nome}' removido com sucesso");
    }
}
