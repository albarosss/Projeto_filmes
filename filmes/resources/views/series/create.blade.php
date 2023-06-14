<x-layout title="Novo Filme">
    <form action="{{ route('series.store') }}" method="post">
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
                <label for="seasonsQty" class="form-label">Descrição:</label>
                <textarea
                id="descFilm"
                name="descFilm"
                class="form-control"
                value="{{ old('descFilm') }}"></textarea>
            </div>

        </div>

        <button type="submit" class="btn btn-primary">Adicionar</button>
    </form>
</x-layout>
