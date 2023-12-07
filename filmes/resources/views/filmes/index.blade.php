<x-layout title="Filmes" :mensagem-sucesso="$mensagemSucesso">

    @auth
    @if ($isAdmin)
        <a href="{{ route('filmes.create') }}" class="adc_filmes btn btn-dark mb-2">Adicionar</a>
        <a href="{{ route('users.list') }}" class="adc_filmes btn btn-dark mb-2">Usuários</a>
    @endif
    @endauth

    <!-- Seção para os filmes com melhores avaliações -->
    <div class="best-rated">
        @if ($bestRatedFilmes->isNotEmpty())
            <h3>Filmes melhores avaliados de todos os generos!</h3>
            <div class="pagination-container">
                @if ($bestRatedFilmes->currentPage() > 1)
                    <button class="pagination-button prev" onclick="pageAnt()">
                        <
                    </button>
                @endif

                <ul class="list-group">
                    <li class="list-group-item d-flex" style="justify-content: space-evenly; min-width: 1225px;">
                        @foreach ($bestRatedFilmes as $filme)
                            <div class="card">
                                <img src="{{ $filme->urlimg }}" alt="Card Image" style="width:100%">
                                <div class="card-content">
                                    <h3 class="card-title">{{ $filme->nome }}</h3>
                                    <p class="card-avaliacao">Avaliação: {{ $filme->descricao }}&#9733;</p>
                                    <b><p class="card-avaliacao">Avaliação: {{ $filme->media_avaliacao }}&#9733;</p></b>
                                    <a href="{{ route('filmes.saiba_mais', $filme->id) }}" class="card-button">Saiba mais</a>
                                </div>
                            </div>
                        @endforeach
                    </li>
                </ul>

                @if ($bestRatedFilmes->hasMorePages())
                    <button class="pagination-button next" onclick="nextPage()">
                        >
                    </button>
                @endif
            </div>

            {{ $bestRatedFilmes->links('pagination.custom') }}
        @endif
    </div>


    @php $count = 0 @endphp
    <div class="alert alert-warning m-5" id="no-films-message" style="display: none;">
        Não há filmes nesta categoria.
    </div>
    <div class="cards-container">
        @foreach ($filmes as $filme)
            @if ($count % 3 === 0)
                @if ($count > 0)
                    </ul>
                @endif
                <ul class="list-group flex-row">
            @endif
            <li class="d-flex align-items-center" style="margin: 10px; padding: 5px; width: fit-content; height: fit-content;">
                <div class="card filmeC" data-genero="{{ $filme->categoria }}">
                    {{-- <img src="{{ $filme->urlimg }}" alt="Card Image" style="width:100%"> --}}
                    <img src="{{ str_starts_with($filme->urlimg, 'https') ? $filme->urlimg : 'storage/' . ltrim($filme->urlimg, '/') }}" alt="Card Image" style="width:100%">

                    <div class="card-content">
                        <h3 class="card-title">{{ $filme->nome }}</h3>
                        <p style=" overflow-wrap: break-word;
                        word-wrap: break-word; /;"><strong>Descrição:</strong> {{ $filme->descricao }}</p>
                        <a href="{{ route('filmes.saiba_mais', $filme->id) }}" class="card-button">Saiba mais</a>
                    </div>
                </div>

            </li>
            @php $count++ @endphp
        @endforeach
        </ul>
    </div>

    <script src="{{ asset('js/inicial/index.js') }}"></script>

</x-layout>
