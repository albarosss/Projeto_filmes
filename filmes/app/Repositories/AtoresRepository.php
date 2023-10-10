<?php

namespace App\Repositories;

use App\Http\Requests\AtoresFormRequest;
use App\Models\Atores;

interface AtoresRepository
{
    public function add(AtoresFormRequest $request): Atores;
}
