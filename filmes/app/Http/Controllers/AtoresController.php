<?php

namespace App\Http\Controllers;

use App\Models\Atores;
use App\Http\Requests\AtoresFormRequest;
use Illuminate\Http\Request;

class AtoresController extends Controller
{
    public function store(AtoresFormRequest $request)
    {
        $ator = new Atores();
        $ator->nome = $request->input('nome_ator');
        $ator->save();

        return redirect()->route('filmes.create')
            ->with('mensagem.sucesso', "Ator '{$ator->nome}' adicionado com sucesso");
    }

    public function list()
    {
        $atores = Atores::all();
        return response()->json(['atores' => $atores]);
    }

    public function edit($id)
    {
        $ator = Atores::findOrFail($id);
        return view('atores.editar', compact('ator'));
    }

    public function update(AtoresFormRequest $request, $id)
    {
        $ator = Atores::findOrFail($id);
        $ator->nome = $request->input('nome_ator');
        $ator->save();

        return redirect()->route('atores.list')
            ->with('mensagem.sucesso', "Ator '{$ator->nome}' atualizado com sucesso");
    }

    public function destroy($id)
    {
        $ator = Atores::findOrFail($id);
        $ator->delete();

        return redirect()->route('atores.list')
            ->with('mensagem.sucesso', "Ator '{$ator->nome}' excluído com sucesso");
    }
}
