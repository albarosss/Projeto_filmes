<x-layout title="Filmes" :mensagem-sucesso="$mensagemSucesso">

    <div class="cards-container">
        @auth
        @if ($isAdmin)
            <a href="{{ route('filmes.create') }}" class="adc_filmes btn btn-dark mb-2">Adicionar</a>
        @endif
        @endauth
        {{-- <form action="{{ route('filmes.index') }}" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Pesquisar filmes...">
            <button type="submit">Buscar</button>
        </form> --}}

        <!-- Seção para os filmes com melhores avaliações -->


        <div class="best-rated">
            @if ($bestRatedFilmes->isNotEmpty())
                <h3>Filmes melhores Avaliados!</h3>
                <div class="pagination-container"> <!-- Adicione a classe pagination-container aqui -->
                    <button class="pagination-button prev" onclick="pageAnt()" @if ($bestRatedFilmes->currentPage() == 1) disabled @endif>
                        <
                    </button>

                    <ul class="list-group">
                        <li class="list-group-item d-flex" style="justify-content: space-evenly; min-width: 1225px;">
                            @foreach ($bestRatedFilmes as $filme)
                                <div class="card">
                                    <img src="{{ asset('filmes_capa/capa_padrao.avif') }}" alt="Card Image" style="width:100%">
                                    <div class="card-content">
                                        <h3 class="card-title">{{ $filme->nome }}</h3>
                                        <p class="card-description">{{ $filme->resumo }}</p>
                                        <b><p class="card-avaliacao">Avaliação: {{ $filme->media_avaliacao }}&#9733;</p></b>
                                        <a href="{{ route('filmes.saiba_mais', $filme->id) }}" class="card-button">Saiba mais</a>
                                    </div>
                                </div>
                            @endforeach
                        </li>
                    </ul>

                    <button class="pagination-button next" onclick="nextPage()" @if (!$bestRatedFilmes->hasMorePages()) disabled @endif>
                        >
                    </button>
                </div>

                {{ $bestRatedFilmes->links('pagination.custom') }}
            @endif
        </div>




        @php $count = 0 @endphp
        @foreach ($filmes as $filme)
            @if ($count % 3 === 0)
                @if ($count > 0)
                    </ul>
                @endif
                <ul class="list-group flex-row">
            @endif
            <li class="d-flex align-items-center" style="margin: 10px; padding: 5px; width: fit-content; height: fit-content;">
                <div class="card">
                    <img src="{{ asset('filmes_capa/capa_padrao.avif') }}" alt="Card Image" style="width:100%">
                    <div class="card-content">
                        <h3 class="card-title">{{ $filme->nome }}</h3>
                        <p class="card-description">{{ $filme->resumo }}</p>
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
