<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentarios extends Model
{
    public function filme()
    {
        return $this->belongsTo(Filmes::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class); // Substitua "User" pelo nome do modelo de usuário do seu sistema.
    }
}
