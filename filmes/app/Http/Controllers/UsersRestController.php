<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersRestController extends Controller
{
    public function index()
    {
        $users = User::all();

        return response()->json(['users' => $users]);
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado.']);
        }

        return response()->json(['user' => $user]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json(['success' => 'Usuário criado com sucesso', 'user' => $user]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado.']);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json(['success' => "Usuário '{$user->name}' atualizado com sucesso", 'user' => $user]);
    }

    public function destroy($id)
    {
        if (Auth::id() == $id) {
            return response()->json(['error' => 'Você não pode excluir seu próprio usuário.']);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Usuário não encontrado.']);
        }

        $user->delete();

        return response()->json(['success' => "Usuário '{$user->name}' excluído com sucesso"]);
    }
}
