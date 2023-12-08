<?php

namespace App\Http\Controllers;

use App\Http\Requests\AtoresFormRequest;
use App\Models\Atores;
use Illuminate\Support\Facades\Auth;

class AtoresRestController extends Controller
{
    public function index()
    {
        $atores = Atores::all();

        return response()->json(['atores' => $atores]);
    }

    public function show($id)
    {
        $ator = Atores::find($id);

        if (!$ator) {
            return response()->json(['error' => 'Ator não encontrado.']);
        }

        return response()->json(['ator' => $ator]);
    }

    public function store(AtoresFormRequest $request)
    {
        $ator = Atores::create($request->all());

        return response()->json(['success' => "Ator '{$ator->nome}' adicionado com sucesso"]);
    }

    public function update(AtoresFormRequest $request, $id)
    {
        $ator = Atores::find($id);

        if (!$ator) {
            return response()->json(['error' => 'Ator não encontrado.']);
        }

        $ator->update($request->all());

        return response()->json(['success' => "Ator '{$ator->nome}' atualizado com sucesso", 'ator' => $ator]);
    }

    public function destroy($id)
    {
        if (!Auth::check() || !Auth::user()->admin) {
            return response()->json(['error' => 'Você não tem permissão para excluir atores.']);
        }

        $ator = Atores::find($id);

        if (!$ator) {
            return response()->json(['error' => 'Ator não encontrado.']);
        }

        $ator->delete();

        return response()->json(['success' => "Ator '{$ator->nome}' removido com sucesso"]);
    }
}
