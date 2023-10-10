<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class AdminMiddleware
{

    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->admin == 1) {
            return $next($request);
        }

        $erros = new MessageBag(['mensagemErro' => 'Acesso não autorizado.']);

        return redirect()->route('filmes.index')->withErrors($erros);
    }
}


