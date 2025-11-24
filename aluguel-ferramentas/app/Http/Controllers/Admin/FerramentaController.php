<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ferramenta;
use App\Models\Setor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FerramentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Ferramenta::with('setor');

        if ($request->filled('search')) {
            $query->where('nome', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('setor_id')) {
            $query->where('setor_id', $request->setor_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $ferramentas = $query->orderBy('nome')->paginate(10);
        $setores = Setor::orderBy('nome')->get();

        return view('admin.ferramentas.index', compact('ferramentas', 'setores'));
    }

    public function create()
    {
        $setores = Setor::orderBy('nome')->get();
        return view('admin.ferramentas.create', compact('setores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|min:2|max:255',
            'setor_id' => 'required|exists:setores,id',
            'estado' => 'required|in:bom,ruim,manutenção,quebrado',
            'valor_compra' => 'nullable|numeric',
            'foto' => 'nullable|image|max:4096',
            'descricao' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('ferramentas', 'public');
        }

        Ferramenta::create($data);

        return redirect()->route('ferramentas.index')
            ->with('success', 'Ferramenta cadastrada com sucesso!');
    }

    public function edit(Ferramenta $ferramenta)
    {
        $setores = Setor::orderBy('nome')->get();

        return view('admin.ferramentas.edit', compact('ferramenta', 'setores'));
    }

    public function update(Request $request, Ferramenta $ferramenta)
    {
        $request->validate([
            'nome' => 'required|min:2|max:255',
            'setor_id' => 'required|exists:setores,id',
            'estado' => 'required|in:bom,ruim,manutenção,quebrado',
            'valor_compra' => 'nullable|numeric',
            'foto' => 'nullable|image|max:4096',
            'descricao' => 'nullable|string',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            if ($ferramenta->foto) {
                Storage::disk('public')->delete($ferramenta->foto);
            }
            $data['foto'] = $request->file('foto')->store('ferramentas', 'public');
        }

        $ferramenta->update($data);

        return redirect()->route('ferramentas.index')
            ->with('success', 'Ferramenta atualizada!');
    }

    public function destroy(Ferramenta $ferramenta)
    {
        if ($ferramenta->foto) {
            Storage::disk('public')->delete($ferramenta->foto);
        }

        $ferramenta->delete();

        return redirect()->route('ferramentas.index')
            ->with('success', 'Ferramenta removida!');
    }
}
