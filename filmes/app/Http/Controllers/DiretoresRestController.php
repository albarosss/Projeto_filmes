<?php

namespace App\Http\Controllers;


use App\Models\Diretores;
use App\Http\Requests\DiretoresFormRequest;

class DiretoresRestController extends Controller
{
    public function index()
    {
        $diretores = Diretores::all();

        return response()->json(['diretores' => $diretores]);
    }

    public function show($id)
    {
        $diretor = Diretores::find($id);

        if (!$diretor) {
            return response()->json(['error' => 'Diretor não encontrado.']);
        }

        return response()->json(['diretor' => $diretor]);
    }

    public function storeAPI(DiretoresFormRequest $request)
    {
        dd("AQ");
        $diretor = Diretores::create(['nome' => $request->input('nome_diretor')]);

        return response()->json(['success' => "Diretor '{$diretor->nome}' adicionado com sucesso"]);
    }

    public function update(DiretoresFormRequest $request, $id)
    {
        $diretor = Diretores::find($id);

        if (!$diretor) {
            return response()->json(['error' => 'Diretor não encontrado.']);
        }

        $diretor->update(['nome' => $request->input('nome_diretor')]);

        return response()->json(['success' => "Diretor '{$diretor->nome}' atualizado com sucesso", 'diretor' => $diretor]);
    }

    public function destroy($id)
    {
        $diretor = Diretores::find($id);

        if (!$diretor) {
            return response()->json(['error' => 'Diretor não encontrado.']);
        }

        $diretor->delete();

        return response()->json(['success' => "Diretor '{$diretor->nome}' removido com sucesso"]);
    }
}
