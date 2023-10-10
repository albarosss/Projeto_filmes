<?php

namespace App\Providers;

use App\Repositories\EloquentAtoresRepository;
use App\Repositories\AtoresRepository;
use Illuminate\Support\ServiceProvider;

class AtoresRepositoryProvider extends ServiceProvider
{
    public array $bindings = [
        AtoresRepository::class => EloquentAtoresRepository::class
    ];
}
