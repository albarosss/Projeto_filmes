<x-layout title="Novo Filme">
    <form action="{{ route('filmes.store') }}" method="post">
        @csrf

        <div class="row mb-3">
            <div class="col-6">
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
            <div class="col-6">
                <label for="descricao" class="form-label">Descrição:</label>
                <textarea
                id="descricao"
                name="descricao"
                class="form-control"
                value="{{ old('descricao') }}"></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <label for="categoria" class="form-label">Categoria:</label>
                <select id="categoria" name="categoria" class="form-control" value="{{ old('categoria') }}">
                    <option value="acao">Ação</option>
                    <option value="comedia">Comédia</option>
                    <option value="terror">Terror</option>
                    <option value="romance">Romance</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Adicionar</button>
    </form>
</x-layout>
