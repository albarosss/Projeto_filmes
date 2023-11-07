<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
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
        $randomFilm = Filmes::inRandomOrder()->first(); // Obtém um filme aleatório da tabela "filmes"

        if ($randomFilm) {
            return redirect()->route('filmes.saiba_mais', $randomFilm->id); // Redireciona para a página "Saiba mais" do filme aleatório
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
                ->select('filmes.id', 'filmes.nome', 'filmes.urlimg', DB::raw('COALESCE(ROUND(AVG(comentarios.avaliacao), 2), "Não Avaliado!") as media_avaliacao'))
                ->where('categoria', $genero)
                ->groupBy('filmes.id', 'filmes.nome', 'filmes.urlimg')
                ->orderBy('nome', 'asc')
                ->get();

            return response()->json($filmes);
        } else {
            $filmes = Filmes::leftJoin('comentarios', 'filmes.id', '=', 'comentarios.filme_id')
                ->select('filmes.id', 'filmes.nome', 'filmes.urlimg', DB::raw('COALESCE(ROUND(AVG(comentarios.avaliacao), 2), "Não Avaliado!") as media_avaliacao'))
                ->groupBy('filmes.id', 'filmes.nome', 'filmes.urlimg')
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

    public function apiStore()
    {
        $apiKey = 'bf1ddac8920d395547c13e1bad46874c';
        $pageNumber = 1; // Página inicial
        $totalMovies = 0; // Contador de filmes

        do {
            $movieListUrl = "https://api.themoviedb.org/3/movie/popular?api_key={$apiKey}&language=pt-BR&page={$pageNumber}";
            $response = Http::get($movieListUrl);

            if ($response->successful()) {
                $data = $response->json();
                $movies = $data['results'];

                foreach ($movies as $movie) {
                    if ($totalMovies >= 100) {
                        break; // Sai do loop se já tiver 100 filmes cadastrados
                    }

                    $movieName = $movie['title'];

                    // Verifica se o filme já existe no banco de dados pelo nome
                    $existingMovie = Filmes::where('nome', $movieName)->first();

                    if (!$existingMovie) {
                        $movieId = $movie['id'];
                        $creditsUrl = "https://api.themoviedb.org/3/movie/{$movieId}/credits?api_key={$apiKey}&language=pt-BR";
                        $detailsUrl = "https://api.themoviedb.org/3/movie/{$movieId}?api_key={$apiKey}&language=pt-BR";

                        $creditsResponse = Http::get($creditsUrl);
                        $detailsResponse = Http::get($detailsUrl);

                        if ($creditsResponse->successful() && $detailsResponse->successful()) {
                            $creditsData = $creditsResponse->json();
                            $movieDetails = $detailsResponse->json();

                            // Resto do seu código para processar e salvar os filmes
                            $diretor = collect($creditsData['crew'])->first(function ($member) {
                                return $member['department'] === 'Directing';
                            });

                            $atorPrincipal = $creditsData['cast'][0];

                            $diretorNome = $diretor ? $diretor['name'] : 'Diretor desconhecido';
                            $diretorModel = Diretores::firstOrNew(['nome' => $diretorNome]);
                            $diretorModel->save();

                            $atorPrincipalNome = $atorPrincipal ? $atorPrincipal['name'] : 'Ator principal desconhecido';
                            $atorPrincipalModel = Atores::firstOrNew(['nome' => $atorPrincipalNome]);
                            $atorPrincipalModel->save();

                            $filme = new Filmes();
                            $filme->nome = $movieDetails['title'];
                            $filme->fk_diretor = $diretorModel->id;
                            $filme->fk_ator_principal = $atorPrincipalModel->id;
                            $filme->descricao = $movieDetails['overview'] ? $movieDetails['overview'] : "Não encontrada";
                            $filme->categoria = $movieDetails['genres'][0]['name'];
                            $filme->urlimg = "https://image.tmdb.org/t/p/w500{$movieDetails['poster_path']}";
                            $filme->save();

                            $totalMovies++;
                        }
                    }
                }

                $pageNumber++; // Avança para a próxima página
            }
        } while (!empty($movies) && $totalMovies < 100);

        return redirect()->route('filmes.index')
            ->with('mensagem.sucesso', "Total de $totalMovies filmes da API adicionados com sucesso");
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
