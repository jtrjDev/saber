<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ferramenta;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AlmoxarifadoController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Filtros
        $statusFiltro = $request->status ?? null;
        $search = $request->search ?? null;
        
        // Query base com relacionamentos
        $query = Ferramenta::with(['setor', 'itens' => function($q) {
            $q->with(['aluguel.usuario', 'aluguel.casa'])
              ->whereIn('status', ['emprestado', 'renovado', 'parcial'])
              ->orderBy('created_at', 'desc');
        }]);
        
        // APLICA FILTRO POR SETOR (se não for admin)
        if ($user->role !== 'admin') {
            $query->where('setor_id', $user->setor_id);
        }
        
        // Filtro por busca
        if ($search) {
            $query->where('nome', 'like', "%{$search}%");
        }
        
        // Filtro por status
        if ($statusFiltro === 'disponiveis') {
            $query->whereDoesntHave('itens', function($q) {
                $q->whereIn('status', ['emprestado', 'renovado', 'parcial']);
            });
        } elseif ($statusFiltro === 'emprestadas') {
            $query->whereHas('itens', function($q) {
                $q->whereIn('status', ['emprestado', 'renovado', 'parcial']);
            });
        } elseif ($statusFiltro === 'atrasadas') {
            $query->whereHas('itens', function($q) {
                $q->whereIn('status', ['emprestado', 'renovado', 'parcial'])
                  ->whereHas('aluguel', function($q2) {
                      $q2->where('data_prevista', '<', now());
                  });
            });
        }
        
        // Paginação (10 itens por página)
        $ferramentas = $query->paginate(10);
        
        // Processa o último aluguel para cada ferramenta
        $ferramentas->getCollection()->transform(function($ferramenta) {
            $ferramenta->ultimo_aluguel = $ferramenta->itens->first();
            return $ferramenta;
        });
        
        // Calcula estatísticas (com todos os registros, não apenas da página atual)
        $statsQuery = Ferramenta::query();
        
        if ($user->role !== 'admin') {
            $statsQuery->where('setor_id', $user->setor_id);
        }
        
        // Aplica filtro de busca nas estatísticas também
        if ($search) {
            $statsQuery->where('nome', 'like', "%{$search}%");
        }
        
        $allFerramentas = $statsQuery->with(['itens' => function($q) {
            $q->whereIn('status', ['emprestado', 'renovado', 'parcial'])
              ->orderBy('created_at', 'desc');
        }])->get();
        
        // Processa último aluguel para todas
        $allFerramentas->transform(function($ferramenta) {
            $ferramenta->ultimo_aluguel = $ferramenta->itens->first();
            return $ferramenta;
        });
        
        $stats = [
            'total' => $allFerramentas->count(),
            'disponiveis' => $allFerramentas->where('ultimo_aluguel', null)->count(),
            'emprestadas' => $allFerramentas->where('ultimo_aluguel', '!=', null)->count(),
            'atrasadas' => $allFerramentas->filter(function($ferramenta){
                if(!$ferramenta->ultimo_aluguel) return false;
                return now()->gt(Carbon::parse($ferramenta->ultimo_aluguel->aluguel->data_prevista));
            })->count(),
        ];
        
        // Informações do usuário para a view
        $setorUsuario = $user->role !== 'admin' ? $user->setor : null;
        $isAdmin = $user->role === 'admin';
        
        return view('admin.almoxarifado.dashboard', compact(
            'ferramentas', 
            'stats', 
            'setorUsuario', 
            'isAdmin', 
            'statusFiltro', 
            'search'
        ));
    }
    
    /**
     * Mostrar detalhes de uma ferramenta específica
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $ferramenta = Ferramenta::with([
            'setor',
            'itens' => function($q) {
                $q->with(['aluguel.usuario', 'aluguel.casa'])
                  ->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);
        
        // Verifica permissão de setor
        if ($user->role !== 'admin' && $ferramenta->setor_id !== $user->setor_id) {
            return redirect()->route('almoxarifado.index')
                ->with('error', 'Você não tem permissão para ver esta ferramenta!');
        }
        
        // Histórico de empréstimos
        $historico = $ferramenta->itens()
            ->with(['aluguel.usuario', 'aluguel.casa'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('admin.almoxarifado.show', compact('ferramenta', 'historico'));
    }
    
    /**
     * Registrar devolução de ferramenta
     */
    public function devolver($id)
    {
        $user = Auth::user();
        
        $ferramenta = Ferramenta::findOrFail($id);
        
        // Verifica permissão
        if ($user->role !== 'admin' && $ferramenta->setor_id !== $user->setor_id) {
            return redirect()->route('almoxarifado.index')
                ->with('error', 'Você não tem permissão para devolver esta ferramenta!');
        }
        
        // Encontra o item de aluguel ativo
        $itemAtivo = $ferramenta->itens()
            ->whereIn('status', ['emprestado', 'renovado', 'parcial'])
            ->whereHas('aluguel', function($q) {
                $q->where('data_devolucao', null);
            })
            ->first();
        
        if (!$itemAtivo) {
            return redirect()->route('almoxarifado.index')
                ->with('error', 'Esta ferramenta não está emprestada no momento!');
        }
        
        // Atualiza status do item
        $itemAtivo->update([
            'status' => 'devolvido',
            'data_devolucao_real' => now(),
        ]);
        
        // Atualiza o aluguel principal se todos os itens foram devolvidos
        $aluguel = $itemAtivo->aluguel;
        $itensPendentes = $aluguel->itens()
            ->whereIn('status', ['emprestado', 'renovado', 'parcial'])
            ->count();
        
        if ($itensPendentes === 0) {
            $aluguel->update([
                'data_devolucao' => now(),
                'status' => 'finalizado'
            ]);
        }
        
        return redirect()->route('almoxarifado.index')
            ->with('success', 'Ferramenta devolvida com sucesso!');
    }
    
    /**
     * API para dados em tempo real (se precisar)
     */
    public function apiStats()
    {
        $user = Auth::user();
        
        $query = Ferramenta::query();
        
        if ($user->role !== 'admin') {
            $query->where('setor_id', $user->setor_id);
        }
        
        $ferramentas = $query->with(['itens' => function($q) {
            $q->whereIn('status', ['emprestado', 'renovado', 'parcial']);
        }])->get();
        
        $ferramentas->transform(function($ferramenta) {
            $ferramenta->ultimo_aluguel = $ferramenta->itens->first();
            return $ferramenta;
        });
        
        return response()->json([
            'disponiveis' => $ferramentas->where('ultimo_aluguel', null)->count(),
            'emprestadas' => $ferramentas->where('ultimo_aluguel', '!=', null)->count(),
            'atrasadas' => $ferramentas->filter(function($ferramenta){
                if(!$ferramenta->ultimo_aluguel) return false;
                return now()->gt(Carbon::parse($ferramenta->ultimo_aluguel->aluguel->data_prevista));
            })->count(),
        ]);
    }
}