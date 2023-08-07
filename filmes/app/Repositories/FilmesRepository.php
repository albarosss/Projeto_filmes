<?php

namespace App\Repositories;

use App\Http\Requests\FilmesFormRequest;
use App\Models\Filmes;

interface FilmesRepository
{
    public function add(FilmesFormRequest $request): Filmes;
}
