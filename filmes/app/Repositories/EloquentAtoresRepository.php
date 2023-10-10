<?php

namespace App\Repositories;

use App\Http\Requests\AtoresFormRequest;
use App\Models\Atores;
use Illuminate\Support\Facades\DB;

class EloquentAtoresRepository implements AtoresRepository
{
    public function add(AtoresFormRequest $request): Atores
    {
        return DB::transaction(function () use ($request) {
            $ator = Atores::create($request->all());
            return $ator;
        });
    }
}
