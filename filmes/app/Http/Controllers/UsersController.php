<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController
{
    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->except(['_token']);
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        Auth::login($user);

        return to_route('filmes.index');
    }

    public function editar()
    {
        $mensagemSucesso = session('mensagem.sucesso');

        return view('users.edite')->with('mensagemSucesso', $mensagemSucesso);
    }


    public function list()
    {
        $users = User::all();
        $mensagemSucesso = session('mensagem.sucesso');

        return view('users.list', compact('users', 'mensagemSucesso'));
    }

    public function destroy($id)
    {
        if (Auth::id() == $id) {
            return redirect()->route('users.list')->with('mensagem.erro', 'Você não pode excluir seu próprio usuário.');
        }

        $user = User::find($id);

        if (!$user) {
            return redirect()->route('users.list')->with('mensagem.erro', 'Usuário não encontrado.');
        }

        $user->delete();

        return redirect()->route('users.list')->with('mensagem.sucesso', 'Usuário excluído com sucesso.');
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

        $mensagemSucesso = session('mensagem.sucesso');

        session()->forget('mensagem.sucesso');

        return view('users.edite', compact('user', 'mensagemSucesso'))->with('mensagem.sucesso', 'Usuário editado com sucesso.');
    }

}
