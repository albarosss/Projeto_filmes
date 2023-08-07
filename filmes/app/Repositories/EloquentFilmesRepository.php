<?php

namespace App\Repositories;

use App\Http\Requests\FilmesFormRequest;
use App\Models\Filmes;
use Illuminate\Support\Facades\DB;

class EloquentFilmesRepository implements FilmesRepository
{
    public function add(FilmesFormRequest $request): Filmes
    {
        return DB::transaction(function () use ($request) {
            $filme = Filmes::create($request->all());
            return $filme;
        });
    }
}
