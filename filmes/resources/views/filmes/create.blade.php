<x-layout title="Novo Filme" :mensagem-sucesso="$mensagemSucesso">
    <div class="container_formularios">
        <div class="form_filme">
            <h3>Cadastro Filme:</h3>
            <form action="{{ route('filmes.store') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="nome" class="form-label">Nome:</label>
                        <input type="text"
                        autofocus
                        id="nome"
                        name="nome"
                        class="form-control"
                        value="{{ old('nome') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="descricao" class="form-label">Descrição:</label>
                        <textarea
                        id="descricao"
                        name="descricao"
                        class="form-control"
                        value="{{ old('descricao') }}"></textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="categoria" class="form-label">Categoria:</label>
                        <select id="categoria" name="categoria" class="form-control" value="{{ old('categoria') }}">
                            <option value="acao">Ação</option>
                            <option value="comedia">Comédia</option>
                            <option value="terror">Terror</option>
                            <option value="romance">Romance</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="urlimg" class="form-label">Imagem:</label>
                        <input
                        id="urlimg"
                        name="urlimg"
                        class="form-control"
                        type="file"
                        value="{{ old('urlimg') }}"/>
                    </div>
                </div>

                <!-- Select de Ator -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="ator" class="form-label">Ator:</label>
                        <select id="ator" name="fk_ator_principal" class="form-control">
                            @foreach($atores as $ator)
                                <option value="{{ $ator->id }}">{{ $ator->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label for="diretor" class="form-label">Diretor:</label>
                        <select id="diretor" name="fk_diretor" class="form-control">
                            @foreach($diretores as $diretor)
                                <option value="{{ $diretor->id }}">{{ $diretor->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Adicionar</button>
            </form>
        </div>

        <div class="form_ator">
            <h3>Cadastro Ator:</h3>

            <form action="{{ route('atores.store') }}" method="post" style="width: 60%">
                @csrf
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="atornome" class="form-label">Nome:</label>
                        <input type="text" id="atornome" name="nome_ator" class="form-control">
                    </div>
                </div>

                <div class="row mb-1">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" id="adicionarAtor">Adicionar</button>
                    </div>
                </div>

            </form>
        </div>

        <div class="form_diretor">
            <h3>Cadastro Diretor:</h3>

            <form action="{{ route('diretores.store') }}" method="post" style="width: 60%">
                @csrf
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="nomeA" class="form-label">Nome:</label>
                        <input type="text" id="nome_diretor" name="nome_diretor" class="form-control">
                    </div>
                </div>

                <div class="row mb-1">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" id="adicionarDiretor">Adicionar</button>
                    </div>
                </div>

            </form>
        </div>
    </div>



</x-layout>
