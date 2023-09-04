<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filmes extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'descricao', 'categoria','resumo', 'avaliacao'];

    protected static function booted()
    {
        self::addGlobalScope('ordered', function (Builder $queryBuilder) {
            $queryBuilder->orderBy('nome');
        });
    }

    public function comentarios()
    {
        return $this->hasMany(Comentarios::class, 'filme_id'); // Verifique se o segundo parâmetro é 'filme_id'
    }

}
