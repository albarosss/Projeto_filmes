<?php

namespace App\Repositories;

use App\Http\Requests\DiretoresFormRequest;
use App\Models\Diretores;
use Illuminate\Support\Facades\DB;

class EloquentDiretoresRepository implements DiretoresRepository
{
    public function add(DiretoresFormRequest $request): Diretores
    {
        return DB::transaction(function () use ($request) {
            $diretor = Diretores::create($request->all());
            return $diretor;
        });
    }
}
