<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('setor');

        // Filtro de busca
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        // Filtro por role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filtro por setor
        if ($request->filled('setor_id')) {
            $query->where('setor_id', $request->setor_id);
        }

        $users = $query->orderBy('name')->paginate(10);
        $setores = Setor::orderBy('nome')->get();

        return view('admin.usuarios.index', compact('users', 'setores'));
    }

    public function create()
    {
        $setores = Setor::orderBy('nome')->get();
        return view('admin.usuarios.create', compact('setores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|min:2',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,gestor_obra,responsavel_ferramentas,membro',
            'setor_id' => 'nullable|exists:setores,id',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'setor_id' => $request->setor_id,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(User $usuario)
    {
        $setores = Setor::orderBy('nome')->get();
        return view('admin.usuarios.edit', compact('usuario', 'setores'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'     => 'required|min:2',
            'email'    => "required|email|unique:users,email,{$usuario->id}",
            'role'     => 'required|in:admin,gestor_obra,responsavel_ferramentas,membro',
            'setor_id' => 'nullable|exists:setores,id',
        ]);

        $usuario->update([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'setor_id' => $request->setor_id,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário atualizado!');
    }

    public function destroy(User $usuario)
    {
        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuário removido!');
    }
}
