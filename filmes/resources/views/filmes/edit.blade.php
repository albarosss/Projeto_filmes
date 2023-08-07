<x-layout title="Editar Filme '{!! $filme->nome !!}'">
    <x-filmes.form :action="route('filmes.update', $filme->id)" :nome="$filme->nome" :update="true" />
</x-layout>
