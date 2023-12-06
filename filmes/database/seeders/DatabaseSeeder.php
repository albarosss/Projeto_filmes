<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Atores::factory(10)->create();
        \App\Models\Diretores::factory(5)->create();
        \App\Models\Filmes::factory(10)->create();
        \App\Models\User::factory(1)->create();
    }
}
