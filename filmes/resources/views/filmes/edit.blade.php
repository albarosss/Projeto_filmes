<x-layout title="Editar Filme">
    @if(isset($mensagemSucesso))
    <div class="alert alert-success">
        {{ $mensagemSucesso }}
    </div>
    @endif

    <div class="container_formularios">
        <div class="form_filme">
            <h3>Editar Filme:</h3>
            <form action="{{ route('filmes.update', $filme->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT') <!-- Defina o método como PUT para indicar uma atualização -->

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="nome" class="form-label">Nome:</label>
                        <input type="text"
                            autofocus
                            id="nome"
                            name="nome"
                            class="form-control"
                            value="{{ old('nome', $filme->nome) }}"> <!-- Preencha o valor com o nome existente do filme -->
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="descricao" class="form-label">Descrição:</label>
                        <textarea
                            id="descricao"
                            name="descricao"
                            class="form-control">{{ old('descricao', $filme->descricao) }}</textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="categoria" class="form-label">Categoria:</label>
                        <select id="categoria" name="categoria" class="form-control">
                            <option value="acao" {{ old('categoria', $filme->categoria) === 'acao' ? 'selected' : '' }}>Ação</option>
                            <option value="comedia" {{ old('categoria', $filme->categoria) === 'comedia' ? 'selected' : '' }}>Comédia</option>
                            <option value="terror" {{ old('categoria', $filme->categoria) === 'terror' ? 'selected' : '' }}>Terror</option>
                            <option value="romance" {{ old('categoria', $filme->categoria) === 'romance' ? 'selected' : '' }}>Romance</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="resumo" class="form-label">Resumo:</label>
                        <textarea
                            id="resumo"
                            name="resumo"
                            class="form-control">{{ old('resumo', $filme->resumo) }}</textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="urlimg" class="form-label">Imagem:</label>
                        <input
                            id="urlimg"
                            name="urlimg"
                            class="form-control"
                            type="file">
                    </div>
                </div>

                <!-- Select de Ator -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="ator" class="form-label">Ator Principal:</label>
                        <select id="ator" name="fk_ator_principal" class="form-control">
                            @foreach($atores as $ator)
                                <option value="{{ $ator->id }}" {{ old('fk_ator_principal', $filme->fk_ator_principal) == $ator->id ? 'selected' : '' }}>
                                    {{ $ator->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="diretor" class="form-label">Diretor:</label>
                        <select id="diretor" name="fk_diretor" class="form-control">
                            @foreach($diretores as $diretor)
                                <option value="{{ $diretor->id }}" {{ old('fk_diretor', $filme->fk_diretor) == $diretor->id ? 'selected' : '' }}>
                                    {{ $diretor->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Atualizar</button>
            </form>
        </div>
    </div>
</x-layout>
