<x-layout title="Filmes" :mensagem-sucesso="$mensagemSucesso">
    @auth
    <a href="{{ route('series.create') }}" class="btn btn-dark mb-2">Adicionar</a>
    @endauth

    <ul class="list-group flex-row cards-container">
        @foreach ($series as $serie)
        <li class="d-flex align-items-center" style="margin: 10px; padding: 5px; width:fit-content; height:fit-content;" >
            <div class="card">
                <img src="{{ asset('filmes_capa/capa_padrao.avif') }}" alt="Card Image" style="width:100%">
                <div class="card-content">
                    <h3 class="card-title">{{ $serie->nome }}</h3>
                    <p class="card-description">Descrição do filme.</p>
                    <a href="{{ route('seasons.index', $serie->id) }}" class="card-button">Saiba mais</a>
                </div>
            </div>
        </li>
        @endforeach
    </ul>
</x-layout>
