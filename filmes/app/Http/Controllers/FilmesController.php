<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilmesFormRequest;
use App\Models\Filmes;
use App\Repositories\FilmesRepository;
use Illuminate\Http\Request;

class FilmesController extends Controller
{
    public function __construct(private FilmesRepository $repository)
    {
        $this->middleware('auth')->except('index');
    }

    public function index(Request $request)
    {
        $filmes = Filmes::all();
        $mensagemSucesso = session('mensagem.sucesso');

        return view('filmes.index')->with('filmes', $filmes)
            ->with('mensagemSucesso', $mensagemSucesso);
    }

    public function create()
    {
        return view('filmes.create');
    }

    public function store(FilmesFormRequest $request)
    {
        $filme = $this->repository->add($request);

        return to_route('filmes.index')
            ->with('mensagem.sucesso', "Filme '{$filme->nome}' adicionado com sucesso");
    }

    public function destroy(Filmes $filmes)
    {
        $filmes->delete();

        return to_route('filmes.index')
            ->with('mensagem.sucesso', "Filme '{$filmes->nome}' removidao com sucesso");
    }

    public function edit(Filmes $filmes)
    {
        return view('filmes.edit')->with('filme', $filmes);
    }

    public function saibaMais($filmeId)
    {
        $filme = Filmes::find($filmeId);

        if (!$filme) {
            return redirect()->route('filmes.index')->with('error', 'Filme não encontrado.');
        }

        return view('filmes.saiba_mais', ['filme' => $filme]);
    }


    public function update(Filmes $filmes, FilmesFormRequest $request)
    {
        $filmes->fill($request->all());
        $filmes->save();

        return to_route('filmes.index')
            ->with('mensagem.sucesso', "Série '{$filmes->nome}' atualizado com sucesso");
    }
}
