<?php

namespace App\Repositories;

use App\Http\Requests\DiretoresFormRequest;
use App\Models\Diretores;

interface DiretoresRepository
{
    public function add(DiretoresFormRequest $request): Diretores;
}
