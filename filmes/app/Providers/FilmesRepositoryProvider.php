<?php

namespace App\Providers;

use App\Repositories\EloquentFilmesRepository;
use App\Repositories\FilmesRepository;
use Illuminate\Support\ServiceProvider;

class FilmesRepositoryProvider extends ServiceProvider
{
    public array $bindings = [
        FilmesRepository::class => EloquentFilmesRepository::class
    ];
}
