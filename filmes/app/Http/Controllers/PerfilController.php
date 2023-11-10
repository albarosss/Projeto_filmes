<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Importe o modelo User


class PerfilController
{
    public function editar()
    {
        $mensagemSucesso = session('mensagem.sucesso');

        return view('users.edite')->with('mensagemSucesso', $mensagemSucesso);
    }


    public function atualizar(Request $request)
    {

        $user = User::find(auth()->id());
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->save();


        return to_route('editar')
            ->with('mensagem.sucesso', "Perfil atualizado");
    }

}
