<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilmesFormRequest;
use App\Models\Filmes;
use App\Models\Atores;
use App\Models\Diretores;
use App\Models\Comentarios;
use Illuminate\Support\Str;
use App\Repositories\FilmesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FilmesController extends Controller
{
    public function __construct(private FilmesRepository $repository)
    {
        $this->middleware('auth')->except('index', 'saibaMais', 'search', 'genero');
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

        $bestRatedFilmes = DB::table('filmes')
        ->join('comentarios', 'filmes.id', '=', 'comentarios.filme_id')
        ->select('filmes.id', 'filmes.nome', 'filmes.resumo', 'filmes.urlimg', DB::raw('ROUND(AVG(comentarios.avaliacao), 2) as media_avaliacao'))
        ->groupBy('filmes.id', 'filmes.nome', 'filmes.resumo', 'filmes.urlimg')
        ->havingRaw('AVG(comentarios.avaliacao) > 4')
        ->orderByDesc('media_avaliacao')
        ->paginate(3);

        return view('filmes.index', [
            'filmes' => $filmes,
            'bestRatedFilmes' => $bestRatedFilmes, // Passando os filmes com melhores avaliações para a vista
            'title' => 'Lista de Filmes',
            'isAdmin' => $isAdmin,
        ])->with('filmes', $filmes)
        ->with('mensagemSucesso', $mensagemSucesso);
    }

    public function search()
    {
        $filmes = Filmes::all()->sortBy('nome');
        return response()->json($filmes);
    }
    public function genero($genero)
    {
        if ($genero !== 'todos') {
            $filmes = Filmes::leftJoin('comentarios', 'filmes.id', '=', 'comentarios.filme_id')
                ->select('filmes.id', 'filmes.nome', 'filmes.resumo', 'filmes.urlimg', DB::raw('COALESCE(ROUND(AVG(comentarios.avaliacao), 2), "Não Avaliado!") as media_avaliacao'))
                ->where('categoria', $genero)
                ->groupBy('filmes.id', 'filmes.nome', 'filmes.resumo', 'filmes.urlimg')
                ->orderBy('nome', 'asc')
                ->get();

            return response()->json($filmes);
        } else {
            $filmes = Filmes::leftJoin('comentarios', 'filmes.id', '=', 'comentarios.filme_id')
                ->select('filmes.id', 'filmes.nome', 'filmes.resumo', 'filmes.urlimg', DB::raw('COALESCE(ROUND(AVG(comentarios.avaliacao), 2), "Não Avaliado!") as media_avaliacao'))
                ->groupBy('filmes.id', 'filmes.nome', 'filmes.resumo', 'filmes.urlimg')
                ->orderBy('nome', 'asc')
                ->get();

            return response()->json($filmes);
        }
    }


    public function create()
    {
        $mensagemSucesso = session('mensagem.sucesso');
        $atores = Atores::orderBy('nome')->get();
        $diretores = Diretores::orderBy('nome')->get();

        return view('filmes.create', compact('atores', 'diretores', 'mensagemSucesso'));
    }

    public function store(FilmesFormRequest $request)
    {
        if ($request->hasFile('urlimg'))
        {

            $imagem = $request->file('urlimg');
            $nomeOriginal = $imagem->getClientOriginalName();
            $nomeSeguro = Str::slug(pathinfo($nomeOriginal, PATHINFO_FILENAME)) . '.' . $imagem->getClientOriginalExtension();
            $caminhoImagem = $imagem->storeAs('filmes_capa', $nomeSeguro, 'public');


            $filme = $this->repository->add($request);
            $filme->urlimg = $caminhoImagem; // Salva o caminho da imagem
            $filme->save();

            return redirect()->route('filmes.index')
                ->with('mensagem.sucesso', "Filme '{$filme->nome}' adicionado com sucesso");
        }
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

    public function edit($id)
    {
        $filme = Filmes::find($id);
        $atores = Atores::all();
        $diretores = Diretores::all();

        return view('filmes.edit', compact('filme', 'atores', 'diretores'));
    }



    public function saibaMais($filmeId)
    {
        $filme = Filmes::find($filmeId);

        if (!$filme)
        {
            return redirect()->route('filmes.index')->with('error', 'Filme não encontrado.');
        }

        // Carregar os comentários associados a este filme.
        $comentarios = $filme->comentarios;
        $user = auth()->user();
        $isAdmin = $user && $user->admin == 1;

        return view('filmes.saiba_mais', ['filme' => $filme, 'comentarios' => $comentarios, 'isAdmin' => $isAdmin]);
    }

    public function update(Request $request, $filmeId)
    {
        // Encontre o filme que você deseja atualizar com base no $filmeId
        $filme = Filmes::find($filmeId);

        // Verifique se o filme foi encontrado
        if (!$filme) {
            return redirect()->route('filmes.index')->with('error', 'Filme não encontrado.');
        }

        // Atualize os campos do filme com base nos dados do formulário
        $filme->nome = $request->input('nome');
        $filme->descricao = $request->input('descricao');
        $filme->categoria = $request->input('categoria');
        $filme->resumo = $request->input('resumo');
        $filme->fk_ator_principal = $request->input('fk_ator_principal');
        $filme->fk_diretor = $request->input('fk_diretor');

        // Verifique se uma nova imagem foi enviada no formulário
        if ($request->hasFile('urlimg')) {
            $imagem = $request->file('urlimg');
            $nomeOriginal = $imagem->getClientOriginalName();
            $nomeSeguro = Str::slug(pathinfo($nomeOriginal, PATHINFO_FILENAME)) . '.' . $imagem->getClientOriginalExtension();
            $caminhoImagem = $imagem->storeAs('filmes_capa', $nomeSeguro, 'public');
            $filme->urlimg = $caminhoImagem; // Atualize o campo da imagem com o novo caminho
        }

        // Salve as alterações no banco de dados
        $filme->save();

        $mensagemSucesso = 'Filme atualizado com sucesso'; // Defina a mensagem de sucesso aqui
        $atores = Atores::all();
        $diretores = Diretores::all();

        return view('filmes.edit', compact('filme', 'atores', 'diretores', 'mensagemSucesso'));

    }

}
