<?php

namespace App\Providers;

use App\Repositories\EloquentDiretoresRepository;
use App\Repositories\DiretoresRepository;
use Illuminate\Support\ServiceProvider;

class DiretoresRepositoryProvider extends ServiceProvider
{
    public array $bindings = [
        DiretoresRepository::class => EloquentDiretoresRepository::class
    ];
}
