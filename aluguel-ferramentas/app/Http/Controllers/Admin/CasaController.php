<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Casa;
use App\Models\Setor;
use Illuminate\Http\Request;

class CasaController extends Controller
{
    public function index(Request $request)
    {
        $query = Casa::with('setor');

        // Filtro de busca
        if ($request->filled('search')) {
            $query->where('nome', 'like', '%' . $request->search . '%');
        }

        // Filtro por setor
        if ($request->filled('setor_id')) {
            $query->where('setor_id', $request->setor_id);
        }

        $casas = $query->orderBy('nome')->paginate(10);
        $setores = Setor::orderBy('nome')->get();

        return view('admin.casas.index', compact('casas', 'setores'));
    }

    public function create()
    {
        $setores = Setor::orderBy('nome')->get();
        return view('admin.casas.create', compact('setores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|min:2|max:255',
            'setor_id' => 'required|exists:setores,id',
        ]);

        Casa::create($request->all());

        return redirect()->route('casas.index')
            ->with('success', 'Casa de Oração cadastrada com sucesso!');
    }

    public function edit(Casa $casa)
    {
        $setores = Setor::orderBy('nome')->get();
        return view('admin.casas.edit', compact('casa', 'setores'));
    }

    public function update(Request $request, Casa $casa)
    {
        $request->validate([
            'nome' => 'required|min:2|max:255',
            'setor_id' => 'required|exists:setores,id',
        ]);

        $casa->update($request->all());

        return redirect()->route('casas.index')
            ->with('success', 'Casa de Oração atualizada!');
    }

    public function destroy(Casa $casa)
    {
        $casa->delete();

        return redirect()->route('casas.index')
            ->with('success', 'Casa removida!');
    }
}
