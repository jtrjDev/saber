<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ferramenta;
use App\Models\Setor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FerramentaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ferramenta::with('setor');

        // FILTRO POR SETOR (se não for admin)
        if ($user->role !== 'admin') {
            $query->where('setor_id', $user->setor_id);
        }

        // Filtros adicionais
        if ($request->filled('search')) {
            $query->where('nome', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('setor_id')) {
            // Só permite filtrar por outro setor se for admin
            if ($user->role === 'admin') {
                $query->where('setor_id', $request->setor_id);
            }
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $ferramentas = $query->orderBy('nome')->paginate(10);
        
        // Carrega setores baseado na permissão
        if ($user->role === 'admin') {
            $setores = Setor::orderBy('nome')->get();
        } else {
            $setores = Setor::where('id', $user->setor_id)->orderBy('nome')->get();
        }

        return view('admin.ferramentas.index', compact('ferramentas', 'setores'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Se não for admin, só pode criar no próprio setor
        if ($user->role === 'admin') {
            $setores = Setor::orderBy('nome')->get();
        } else {
            $setores = Setor::where('id', $user->setor_id)->orderBy('nome')->get();
        }
        
        return view('admin.ferramentas.create', compact('setores'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'nome' => 'required|min:2|max:255',
            'setor_id' => 'required|exists:setores,id',
            'estado' => 'required|in:bom,ruim,manutenção,quebrado',
            'valor_compra' => 'nullable|numeric',
            'foto' => 'nullable|image|max:4096',
            'descricao' => 'nullable|string',
        ]);

        // VERIFICA PERMISSÃO DE SETOR
        if ($user->role !== 'admin' && $request->setor_id != $user->setor_id) {
            return redirect()->back()
                ->with('error', 'Você só pode cadastrar ferramentas no seu setor!')
                ->withInput();
        }

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
        $user = Auth::user();
        
        // VERIFICA PERMISSÃO PARA EDITAR
        if ($user->role !== 'admin' && $ferramenta->setor_id != $user->setor_id) {
            return redirect()->route('ferramentas.index')
                ->with('error', 'Você não tem permissão para editar esta ferramenta!');
        }
        
        if ($user->role === 'admin') {
            $setores = Setor::orderBy('nome')->get();
        } else {
            $setores = Setor::where('id', $user->setor_id)->orderBy('nome')->get();
        }

        return view('admin.ferramentas.edit', compact('ferramenta', 'setores'));
    }

    public function update(Request $request, Ferramenta $ferramenta)
    {
        $user = Auth::user();
        
        // VERIFICA PERMISSÃO PARA EDITAR
        if ($user->role !== 'admin' && $ferramenta->setor_id != $user->setor_id) {
            return redirect()->route('ferramentas.index')
                ->with('error', 'Você não tem permissão para editar esta ferramenta!');
        }
        
        $request->validate([
            'nome' => 'required|min:2|max:255',
            'setor_id' => 'required|exists:setores,id',
            'estado' => 'required|in:bom,ruim,manutenção,quebrado',
            'valor_compra' => 'nullable|numeric',
            'foto' => 'nullable|image|max:4096',
            'descricao' => 'nullable|string',
        ]);

        // VERIFICA SE NÃO ADMIN ESTÁ TENTANDO MUDAR DE SETOR
        if ($user->role !== 'admin' && $request->setor_id != $user->setor_id) {
            return redirect()->back()
                ->with('error', 'Você não pode transferir ferramentas para outro setor!')
                ->withInput();
        }

        $data = $request->all();

        if ($request->hasFile('foto')) {
            if ($ferramenta->foto) {
                Storage::disk('public')->delete($ferramenta->foto);
            }
            $data['foto'] = $request->file('foto')->store('ferramentas', 'public');
        }

        $ferramenta->update($data);

        return redirect()->route('ferramentas.index')
            ->with('success', 'Ferramenta atualizada com sucesso!');
    }

    public function destroy(Ferramenta $ferramenta)
    {
        $user = Auth::user();
        
        // VERIFICA PERMISSÃO PARA EXCLUIR
        if ($user->role !== 'admin' && $ferramenta->setor_id != $user->setor_id) {
            return redirect()->route('ferramentas.index')
                ->with('error', 'Você não tem permissão para excluir esta ferramenta!');
        }
        
        // VERIFICA SE A FERRAMENTA ESTÁ EMPRESTADA
        $estaEmprestada = $ferramenta->itens()
            ->whereIn('status', ['emprestado', 'renovado', 'parcial'])
            ->exists();
        
        if ($estaEmprestada) {
            return redirect()->route('ferramentas.index')
                ->with('error', 'Não é possível excluir uma ferramenta que está emprestada!');
        }

        if ($ferramenta->foto) {
            Storage::disk('public')->delete($ferramenta->foto);
        }

        $ferramenta->delete();

        return redirect()->route('ferramentas.index')
            ->with('success', 'Ferramenta removida com sucesso!');
    }
    
    /**
     * Mostrar detalhes de uma ferramenta específica
     */
    public function show(Ferramenta $ferramenta)
    {
        $user = Auth::user();
        
        // VERIFICA PERMISSÃO PARA VISUALIZAR
        if ($user->role !== 'admin' && $ferramenta->setor_id != $user->setor_id) {
            return redirect()->route('ferramentas.index')
                ->with('error', 'Você não tem permissão para visualizar esta ferramenta!');
        }
        
        // Carrega relacionamentos
        $ferramenta->load(['setor', 'itens.aluguel.usuario', 'itens.aluguel.casa']);
        
        // Histórico de empréstimos
        $historico = $ferramenta->itens()
            ->with(['aluguel.usuario', 'aluguel.casa'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('admin.ferramentas.show', compact('ferramenta', 'historico'));
    }
    
    /**
     * Clonar ferramenta (útil para criar cópias)
     */
    public function duplicate(Ferramenta $ferramenta)
    {
        $user = Auth::user();
        
        // VERIFICA PERMISSÃO
        if ($user->role !== 'admin' && $ferramenta->setor_id != $user->setor_id) {
            return redirect()->route('ferramentas.index')
                ->with('error', 'Você não tem permissão para clonar esta ferramenta!');
        }
        
        // Cria cópia
        $novaFerramenta = $ferramenta->replicate();
        $novaFerramenta->nome = $ferramenta->nome . ' (cópia)';
        $novaFerramenta->save();
        
        return redirect()->route('ferramentas.edit', $novaFerramenta)
            ->with('success', 'Ferramenta clonada com sucesso! Edite o nome conforme necessário.');
    }
    
    /**
     * Exportar ferramentas do setor para CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $query = Ferramenta::with('setor');
        
        // Filtro por setor
        if ($user->role !== 'admin') {
            $query->where('setor_id', $user->setor_id);
        }
        
        // Aplica outros filtros
        if ($request->filled('search')) {
            $query->where('nome', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        $ferramentas = $query->orderBy('nome')->get();
        
        // Nome do arquivo
        $fileName = 'ferramentas_' . date('Y-m-d_His') . '.csv';
        
        // Headers do CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];
        
        // Dados
        $callback = function() use ($ferramentas) {
            $file = fopen('php://output', 'w');
            
            // Adiciona cabeçalho
            fputcsv($file, ['ID', 'Nome', 'Setor', 'Estado', 'Descrição', 'Data Cadastro']);
            
            // Adiciona os dados
            foreach ($ferramentas as $ferramenta) {
                fputcsv($file, [
                    $ferramenta->id,
                    $ferramenta->nome,
                    $ferramenta->setor->nome ?? 'N/A',
                    $ferramenta->estado,
                    $ferramenta->descricao ?? '',
                    $ferramenta->created_at->format('d/m/Y'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}