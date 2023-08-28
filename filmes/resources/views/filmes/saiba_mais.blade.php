<x-layout title="Informações do filme">
    <h3 class="titulo">Informações do filme</h3>
    <div class="card_v">
        <img src="{{ asset('filmes_capa/capa_padrao.avif') }}" alt="Card Image">
        {{-- <img src="{{ $filme->imagem }}" alt="Imagem do filme"> --}}
        <div class="card_v-body">
            <div class="infos_filme">
                <p><strong>Título:</strong> {{ $filme->nome }}</p>
                <p><strong>Descrição:</strong> {{ $filme->descricao }}</p>
                <p><strong>Categoria:</strong> {{ $filme->categoria }}</p>
            </div>
        </div>


    </div>
</x-layout>
