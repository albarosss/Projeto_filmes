<x-layout title="Informações do filme">
    @auth
    @if ($isAdmin)
      <a href="{{ route('filmes.edit', $filme) }}" class="adc_filmes btn btn-dark mb-2">Editar</a>
    @endif
    @endauth
    <h3 class="titulo">Informações do filme!</h3>
    <div class="card_v">
        {{-- <img src="{{ asset('filmes_capa/capa_padrao.avif') }}" alt="Card Image"> --}}
        <img src="{{ asset('storage/' . ($filme->urlimg ?? 'filmes_capa/capa_padrao.avif')) }}" alt="Card Image" >
        <div class="card_v-body">
            <div class="infos_filme">
                <p><strong>Título:</strong> {{ $filme->nome }}</p>
                <p style=" overflow-wrap: break-word;
                word-wrap: break-word; /;"><strong>Descrição:</strong> {{ $filme->descricao }}</p>
                <p><strong>Categoria:</strong> {{ $filme->categoria }}</p>
                <p><strong>Ator principal:</strong> {{ $filme->atorPrincipal->nome }}</p>
                <p><strong>Diretor:</strong> {{ $filme->diretorPrincipal->nome }}</p>
            </div>
        </div>
    </div>

    <div class="espacamento"></div>

    <div class="comentarios">

        <h4>Comentários:</h4>

        @if ($filme->comentarios->isEmpty())
            <p>Seja o primeiro a comentar!</p>
        @else
            <?php $lado = 'esquerda'; ?>
            <div class="comentarios-container">
                @foreach ($filme->comentarios->sortByDesc('created_at') as $comentario)
                    <div class="comentario {{ $lado }}">
                        <div class="comentario-content">
                            <p class="comentario-text">
                                <strong>{{ $comentario->usuario->name }}:</strong>
                                <span class="text-{{ ($lado === 'esquerda') ? 'right' : 'left' }}"> <!-- Adicione um estilo inline para alinhar o texto à direita -->
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $comentario->avaliacao)
                                            <span class="starC active">&#9733;</span>
                                        @else
                                            <span class="starC ">&#9733;</span>
                                        @endif
                                    @endfor
                                </span>
                                {{ $comentario->comentario }}
                            </p>
                        </div>
                    </div>
                    <?php $lado = ($lado === 'esquerda') ? 'direita' : 'esquerda'; ?>
                @endforeach
            </div>
         @endif


        <form action="{{ route('filmes.comentar', $filme->id) }}" method="POST" class="comentar-form">
            @csrf
            <div class="form-group">
                <label for="avaliacao">Avaliação:</label>
                <div class="rating">
                    <span class="star active" data-rating="1">&#9733;</span>
                    <span class="star" data-rating="2">&#9733;</span>
                    <span class="star" data-rating="3">&#9733;</span>
                    <span class="star" data-rating="4">&#9733;</span>
                    <span class="star" data-rating="5">&#9733;</span>
                    <input type="hidden" name="avaliacao" id="avaliacao" value="1">
                </div>
            </div>
            <div class="comentario-container">
                <textarea name="comentario" id="comentarB" placeholder="Deixe seu comentário"></textarea>
                <button type="submit" class="enviar-btn">Enviar</button>
            </div>
        </form>
    </div>

    <script src="{{ asset('js/saiba-mais/index.js') }}"></script>

</x-layout>
