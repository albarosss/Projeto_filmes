<x-layout title="Informações do filme">
    <p><strong>Título:</strong> {{ $filme->nome }}</p>
    <p><strong>Descrição:</strong> {{ $filme->descricao }}</p>
    <p><strong>Categoria:</strong> {{ $filme->categoria }}</p>
</x-layout>
