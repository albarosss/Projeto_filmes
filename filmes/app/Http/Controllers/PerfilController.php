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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);


        $user = User::find(auth()->id());
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return to_route('editar')
            ->with('mensagem.sucesso', "Perfil atualizado");
    }

}
