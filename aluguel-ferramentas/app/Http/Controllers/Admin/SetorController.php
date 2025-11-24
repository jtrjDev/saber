<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setor;
use Illuminate\Http\Request;

class SetorController extends Controller
{
    public function index(Request $request)
    {
        $query = Setor::query();

        if ($request->filled('search')) {
            $query->where('nome', 'like', '%' . $request->search . '%');
        }

        $setores = $query->orderBy('nome')->paginate(10);

        return view('admin.setores.index', compact('setores'));
    }

    public function create()
    {
        return view('admin.setores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|min:2|max:255'
        ]);

        Setor::create($request->all());

        return redirect()->route('setores.index')->with('success', 'Setor criado com sucesso!');
    }

    public function edit(Setor $setor)
    {
        return view('admin.setores.edit', compact('setor'));
    }

    public function update(Request $request, Setor $setor)
    {
        $request->validate([
            'nome' => 'required|string|min:2|max:255'
        ]);

        $setor->update($request->all());

        return redirect()->route('setores.index')->with('success', 'Setor atualizado!');
    }

    public function destroy(Setor $setor)
    {
        $setor->delete();

        return redirect()->route('setores.index')->with('success', 'Setor removido.');
    }
}
