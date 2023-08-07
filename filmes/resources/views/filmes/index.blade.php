<x-layout title="Filmes" :mensagem-sucesso="$mensagemSucesso">
    @auth
    <a href="{{ route('filmes.create') }}" class="btn btn-dark mb-2">Adicionar</a>
    @endauth

    <div class="cards-container">
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
                        <p class="card-description">Descrição do filme.</p>
                        <a href="{{ route('filmes.saiba_mais', $filme->id) }}" class="card-button">Saiba mais</a>
                    </div>
                </div>
            </li>
            @php $count++ @endphp
        @endforeach
        </ul>
    </div>
</x-layout>
