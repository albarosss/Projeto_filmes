<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilmesFormRequest;
use App\Models\Filmes;
use App\Models\Comentarios;
use App\Repositories\FilmesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FilmesController extends Controller
{
    public function __construct(private FilmesRepository $repository)
    {
        $this->middleware('auth')->except('index', 'saibaMais');
    }

    public function comentar(Request $request, Filmes $filmes)
    {
        // Validar o formulário (adapte isso de acordo com suas regras de validação).
        $request->validate([
            'comentario' => 'required|string|max:500',
            'avaliacao' => 'required|integer|min:1|max:5',
        ]);

        // Salvar o comentário no banco de dados.
        $comentario = new Comentarios();
        $comentario->filme_id = $filmes->id; // Supondo que você tem um relacionamento Filme-Comentario no modelo.
        $comentario->usuario_id = Auth::id();
        $comentario->comentario = $request->input('comentario');
        $comentario->avaliacao = $request->input('avaliacao');
        $comentario->save();

        return redirect()->back()->with('success', 'Comentário adicionado com sucesso!');

    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user && $user->admin == 1;

        $filmes = Filmes::all();
        $mensagemSucesso = session('mensagem.sucesso');

        $bestRatedFilmes = $bestRatedFilmes = DB::table('filmes')
        ->join('comentarios', 'filmes.id', '=', 'comentarios.filme_id')
        ->select('filmes.id', 'filmes.nome', 'filmes.resumo', DB::raw('ROUND(AVG(comentarios.avaliacao), 2) as media_avaliacao'))
        ->groupBy('filmes.id', 'filmes.nome', 'filmes.resumo')
        ->havingRaw('AVG(comentarios.avaliacao) > 4')
        ->orderByDesc('media_avaliacao')
        ->paginate(3); // Defina o número de filmes por página (3 no seu caso)

        return view('filmes.index', [
            'filmes' => $filmes,
            'bestRatedFilmes' => $bestRatedFilmes, // Passando os filmes com melhores avaliações para a vista
            'title' => 'Lista de Filmes',
            'isAdmin' => $isAdmin,
        ])->with('filmes', $filmes)
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

        // Carregar os comentários associados a este filme.
        $comentarios = $filme->comentarios;

        return view('filmes.saiba_mais', ['filme' => $filme, 'comentarios' => $comentarios]);
    }



    public function update(Filmes $filmes, FilmesFormRequest $request)
    {
        $filmes->fill($request->all());
        $filmes->save();

        return to_route('filmes.index')
            ->with('mensagem.sucesso', "Série '{$filmes->nome}' atualizado com sucesso");
    }
}
