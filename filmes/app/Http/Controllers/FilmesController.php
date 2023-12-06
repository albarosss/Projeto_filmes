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
        $this->middleware('auth')->except('index', 'saibaMais', 'search', 'genero', 'getRandom');
    }

    public function comentar(Request $request, Filmes $filmes)
    {
        $request->validate([
            'comentario' => 'required|string|max:500',
            'avaliacao' => 'required|integer|min:1|max:5',
        ]);

        // Salvar o comentário no banco de dados.
        $comentario = new Comentarios();
        $comentario->filme_id = $filmes->id;
        $comentario->usuario_id = Auth::id();
        $comentario->comentario = $request->input('comentario');
        $comentario->avaliacao = $request->input('avaliacao');
        $comentario->save();

        return redirect()->back()->with('success', 'Comentário adicionado com sucesso!');

    }

    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user && $user->admin == 1;

        $filmes = Filmes::all();
        $mensagemSucesso = session('mensagem.sucesso');

        $bestRatedFilmes = DB::table('filmes')
        ->join('comentarios', 'filmes.id', '=', 'comentarios.filme_id')
        ->select('filmes.id', 'filmes.nome', 'filmes.urlimg', DB::raw('ROUND(AVG(comentarios.avaliacao), 2) as media_avaliacao'))
        ->groupBy('filmes.id', 'filmes.nome', 'filmes.urlimg')
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
    public function getRandom()
    {
        $randomFilm = Filmes::inRandomOrder()->first();

        if ($randomFilm) {
            return redirect()->route('filmes.saiba_mais', $randomFilm->id);
        } else {
            return false;
        }
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
                ->select('filmes.id', 'filmes.nome', 'filmes.urlimg', 'filmes.descricao', DB::raw('COALESCE(ROUND(AVG(comentarios.avaliacao), 2), "Não Avaliado!") as media_avaliacao'))
                ->where('categoria', $genero)
                ->groupBy('filmes.id', 'filmes.nome', 'filmes.urlimg', 'filmes.descricao')
                ->orderBy('nome', 'asc')
                ->get();

            return response()->json($filmes);
        } else {
            $filmes = Filmes::leftJoin('comentarios', 'filmes.id', '=', 'comentarios.filme_id')
                ->select('filmes.id', 'filmes.nome', 'filmes.urlimg','filmes.descricao', DB::raw('COALESCE(ROUND(AVG(comentarios.avaliacao), 2), "Não Avaliado!") as media_avaliacao'))
                ->groupBy('filmes.id', 'filmes.nome', 'filmes.urlimg', 'filmes.descricao')
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
            $filme->urlimg = $caminhoImagem;
            $filme->save();

            return redirect()->route('filmes.index')
                ->with('mensagem.sucesso', "Filme '{$filme->nome}' adicionado com sucesso");
        }
        $filme = $this->repository->add($request);

        return to_route('filmes.index')
            ->with('mensagem.sucesso', "Filme '{$filme->nome}' adicionado com sucesso");
    }

    public function apiStore(Request $request)
    {
        set_time_limit(0);

        $filmes = $request->json()->all();

        foreach ($filmes['nome'] as $key => $nome) {
            $filme = [
                'nome' => $nome,
                'diretor' => $filmes['diretor'][$key],
                'atorPrincipal' => $filmes['atorPrincipal'][$key],
                'descricao' => $filmes['descricao'][$key],
                'categoria' => $filmes['categoria'][$key],
                'imagemUrl' => $filmes['imagemUrl'][$key],
            ];
            if ($this->validarFilme($filme)) {
                $this->salvarDiretor($filme['diretor']);
                $this->salvarAtorPrincipal($filme['atorPrincipal']);
                $this->salvarFilme($filme);
            } else {
                return response()->json(['mensagem' => 'Dados inválidos']);
            }
        }

        return response()->json(['mensagem' => 'Filmes salvos com sucesso']);

    }

    private function validarFilme($filme)
    {
        return isset($filme['nome']) && isset($filme['diretor']) && isset($filme['atorPrincipal']);
    }

    private function salvarDiretor($diretorNome)
    {
        $diretorModel = Diretores::firstOrNew(['nome' => $diretorNome]);
        $diretorModel->save();
    }

    private function salvarAtorPrincipal($atorPrincipalNome)
    {
        $atorPrincipalModel = Atores::firstOrNew(['nome' => $atorPrincipalNome]);
        $atorPrincipalModel->save();
    }

    private function salvarFilme($filme)
    {
        $diretorModel = Diretores::where('nome', $filme['diretor'])->first();
        $atorPrincipalModel = Atores::where('nome', $filme['atorPrincipal'])->first();

        $filmeModel = Filmes::firstOrNew(['nome' => $filme['nome']]);
        $filmeModel->fk_diretor = $diretorModel->id;
        $filmeModel->fk_ator_principal = $atorPrincipalModel->id;
        $filmeModel->descricao = $filme['descricao'];
        $filmeModel->categoria = $filme['categoria'];
        $filmeModel->urlimg = $filme['imagemUrl'];
        $filmeModel->save();
    }

    public function destroy($id)
    {
        if (!Auth::check() || !Auth::user()->admin) {
            return redirect()->route('filmes.index')->with('mensagem.erro', 'Você não tem permissão para excluir filmes.');
        }

        $filme = Filmes::find($id);

        if (!$filme) {
            return redirect()->route('filmes.index')->with('mensagem.erro', 'Filme não encontrado.');
        }

        $filme->delete();

        return redirect()->route('filmes.index')->with('mensagem.sucesso', "Filme '{$filme->nome}' removido com sucesso");
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

        $comentarios = $filme->comentarios;
        $user = auth()->user();
        $isAdmin = $user && $user->admin == 1;

        return view('filmes.saiba_mais', ['filme' => $filme, 'comentarios' => $comentarios, 'isAdmin' => $isAdmin]);
    }

    public function update(Request $request, $filmeId)
    {
        $filme = Filmes::find($filmeId);

        if (!$filme) {
            return redirect()->route('filmes.index')->with('error', 'Filme não encontrado.');
        }

        $filme->nome = $request->input('nome');
        $filme->descricao = $request->input('descricao');
        $filme->categoria = $request->input('categoria');
        $filme->fk_ator_principal = $request->input('fk_ator_principal');
        $filme->fk_diretor = $request->input('fk_diretor');

        if ($request->hasFile('urlimg')) {
            $imagem = $request->file('urlimg');
            $nomeOriginal = $imagem->getClientOriginalName();
            $nomeSeguro = Str::slug(pathinfo($nomeOriginal, PATHINFO_FILENAME)) . '.' . $imagem->getClientOriginalExtension();
            $caminhoImagem = $imagem->storeAs('filmes_capa', $nomeSeguro, 'public');
            $filme->urlimg = $caminhoImagem;
        }

        $filme->save();

        $mensagemSucesso = 'Filme atualizado com sucesso';
        $atores = Atores::all();
        $diretores = Diretores::all();

        return view('filmes.edit', compact('filme', 'atores', 'diretores', 'mensagemSucesso'));

    }

}
