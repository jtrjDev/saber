<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aluguel;
use App\Models\AluguelItem;
use App\Models\Ferramenta;
use App\Models\User;
use App\Models\Casa;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AluguelController extends Controller
{
    private function calcularDataPrevista($dataRetirada, $periodo)
{
    $data = Carbon::parse($dataRetirada);

    // Mapeamento para meses e anos
    $mapaMeses = [
        '1 mes' => 1,
        '2 meses' => 2,
        '3 meses' => 3,
        '4 meses' => 4,
        '6 meses' => 6,
        '1 ano' => 12,
    ];
    
    // Verifica se é mês ou ano
    if (isset($mapaMeses[$periodo])) {
        return $data->addMonths($mapaMeses[$periodo]);
    }

    // Semanas
    if (str_contains($periodo, "semana")) {
        $numero = (int) filter_var($periodo, FILTER_SANITIZE_NUMBER_INT);
        return $data->addWeeks($numero);
    }

    // Dias
    if (str_contains($periodo, "dia")) {
        $numero = (int) filter_var($periodo, FILTER_SANITIZE_NUMBER_INT);
        return $data->addDays($numero);
    }
    
    // Se for um número personalizado (ex: "61 dias")
    if (str_contains($periodo, "dias") && !in_array($periodo, ['2 dias', '3 dias', '4 dias', '5 dias'])) {
        $numero = (int) filter_var($periodo, FILTER_SANITIZE_NUMBER_INT);
        return $data->addDays($numero);
    }

    return $data;
}

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Aluguel::with(['casa', 'usuario', 'responsavel'])->orderBy('id', 'desc');
        
        // FILTRO POR SETOR (se não for admin)
        if ($user->role !== 'admin') {
            // Filtra aluguéis que pertencem a casas do setor do usuário
            $query->whereHas('casa', function($q) use ($user) {
                $q->where('setor_id', $user->setor_id);
            });
        }

        if ($request->filled('search')) {
            $query->whereHas('usuario', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $alugueis = $query->paginate(10);

        return view('admin.alugueis.index', compact('alugueis'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // FILTRA CASAS por setor
        if ($user->role === 'admin') {
            $casas = Casa::orderBy('nome')->get();
        } else {
            $casas = Casa::where('setor_id', $user->setor_id)->orderBy('nome')->get();
        }
        
        $responsaveis = User::where('role', 'responsavel_ferramentas')->get();
        $usuarios = User::where('role', 'membro')
                        ->orWhere('role', 'gestor_obra')
                        ->get();
        
        // FILTRA FERRAMENTAS por setor e disponíveis
        $ferramentasQuery = Ferramenta::whereIn('estado', ['bom', 'nova'])
            ->whereDoesntHave('itens', function($q) {
                $q->whereHas('aluguel', function($sub) {
                    $sub->whereNull('data_devolucao');
                });
            });
        
        if ($user->role !== 'admin') {
            $ferramentasQuery->where('setor_id', $user->setor_id);
        }
        
        $ferramentas = $ferramentasQuery->orderBy('nome')->get();

        return view('admin.alugueis.create', compact('casas', 'responsaveis', 'usuarios', 'ferramentas'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'casa_id' => 'required|exists:casas,id',
            'responsavel_id' => 'required|exists:users,id',
            'data_retirada' => 'required|date',
            'alugar_por' => 'required|string',
            'ferramentas' => 'required|array|min:1',
            'ferramentas.*.id' => 'required|exists:ferramentas,id',
        ]);

        // VERIFICA SE A CASA PERTENCE AO SETOR DO USUÁRIO
        $casa = Casa::find($request->casa_id);
        if ($user->role !== 'admin' && $casa->setor_id != $user->setor_id) {
            return redirect()->back()
                ->with('error', 'Você não pode criar aluguéis para esta casa!')
                ->withInput();
        }

        // Criar usuário caso seja "Outro"
        if ($request->user_id === "outro") {
            $nome = trim($request->novo_usuario);
            $novoUser = User::create([
                'name' => $nome,
                'email' => strtolower(str_replace(' ', '', $nome)) . "@temporario.local",
                'password' => Hash::make('123456'),
                'role' => 'membro'
            ]);
            $userId = $novoUser->id;
        } else {
            $userId = $request->user_id;
        }

        // Calcular data prevista
        $dataPrevista = $this->calcularDataPrevista($request->data_retirada, $request->alugar_por);

        // Criar o aluguel
        $aluguel = Aluguel::create([
            'casa_id' => $request->casa_id,
            'user_id' => $userId,
            'responsavel_id' => $request->responsavel_id,
            'data_retirada' => $request->data_retirada,
            'data_prevista' => $dataPrevista,
            'status' => 'emprestado',
        ]);

        // Criar itens do aluguel
        foreach ($request->ferramentas as $item) {
            $ferramenta = Ferramenta::find($item['id']);
            
            // VERIFICA SE A FERRAMENTA PERTENCE AO SETOR DO USUÁRIO
            if ($user->role !== 'admin' && $ferramenta->setor_id != $user->setor_id) {
                continue; // Pula esta ferramenta se não tiver permissão
            }
            
            AluguelItem::create([
                'aluguel_id' => $aluguel->id,
                'ferramenta_id' => $item['id'],
                'quantidade' => $item['quantidade'] ?? 1,
                'observacao' => $item['observacao'] ?? null,
                'status' => 'emprestado',
            ]);
        }

        return redirect()->route('alugueis.show', $aluguel->id)
            ->with('success', 'Aluguel criado com sucesso!');
    }

    public function show(Aluguel $aluguel)
    {
        $user = Auth::user();
        
        // VERIFICA PERMISSÃO PARA VISUALIZAR
        if ($user->role !== 'admin' && $aluguel->casa->setor_id != $user->setor_id) {
            return redirect()->route('alugueis.index')
                ->with('error', 'Você não tem permissão para visualizar este aluguel!');
        }
        
        $aluguel->load(['casa', 'usuario', 'responsavel', 'itens.ferramenta']);
        return view('admin.alugueis.show', compact('aluguel'));
    }

    public function edit(Aluguel $aluguel)
{
    $user = Auth::user();
    
    // VERIFICA PERMISSÃO PARA EDITAR
    if ($user->role !== 'admin' && $aluguel->casa->setor_id != $user->setor_id) {
        return redirect()->route('alugueis.index')
            ->with('error', 'Você não tem permissão para editar este aluguel!');
    }
    
    $aluguel->load(['itens', 'itens.ferramenta']);

    // FILTRA CASAS por setor
    if ($user->role === 'admin') {
        $casas = Casa::orderBy('nome')->get();
    } else {
        $casas = Casa::where('setor_id', $user->setor_id)->orderBy('nome')->get();
    }
    
    $responsaveis = User::where('role', 'responsavel_ferramentas')->get();
    $usuarios = User::whereIn('role', ['membro', 'gestor_obra'])->get();
    $itensAtuais = $aluguel->itens->pluck('ferramenta_id')->toArray();

    // FILTRA FERRAMENTAS por setor
    $ferramentasQuery = Ferramenta::where(function($query) use ($itensAtuais) {
        $query->whereIn('estado', ['bom', 'nova'])
            ->whereDoesntHave('itens', function($q) {
                $q->whereHas('aluguel', function($sub) {
                    $sub->whereNull('data_devolucao');
                });
            })
            ->orWhereIn('id', $itensAtuais);
    });
    
    if ($user->role !== 'admin') {
        $ferramentasQuery->where('setor_id', $user->setor_id);
    }
    
    $ferramentas = $ferramentasQuery->orderBy('nome')->get();
    
    // Pega o período atual formatado
    $periodoAtual = $aluguel->periodo_select;
    $diasTotais = Carbon::parse($aluguel->data_retirada)->diffInDays(Carbon::parse($aluguel->data_prevista));

    return view('admin.alugueis.edit', compact(
        'aluguel', 'casas', 'responsaveis', 'usuarios', 'ferramentas', 'periodoAtual', 'diasTotais'
    ));
}

    public function update(Request $request, Aluguel $aluguel)
    {
        $user = Auth::user();
        
        // VERIFICA PERMISSÃO
        if ($user->role !== 'admin' && $aluguel->casa->setor_id != $user->setor_id) {
            return redirect()->route('alugueis.index')
                ->with('error', 'Você não tem permissão para editar este aluguel!');
        }
        
        $request->validate([
            'casa_id' => 'required|exists:casas,id',
            'user_id' => 'required',
            'responsavel_id' => 'required|exists:users,id',
            'data_retirada' => 'required|date',
            'alugar_por' => 'required|string',
            'ferramentas.*.id' => 'required|exists:ferramentas,id',
        ]);

        // Atualiza dados gerais
        $aluguel->update([
            'casa_id' => $request->casa_id,
            'user_id' => $request->user_id,
            'responsavel_id' => $request->responsavel_id,
            'data_retirada' => $request->data_retirada,
            'data_prevista' => $this->calcularDataPrevista($request->data_retirada, $request->alugar_por),
            'status' => $aluguel->status,
        ]);

        // Atualiza itens
        $aluguel->itens()->delete();

        foreach ($request->ferramentas as $item) {
            $ferramenta = Ferramenta::find($item['id']);
            
            if ($user->role !== 'admin' && $ferramenta->setor_id != $user->setor_id) {
                continue;
            }
            
            $aluguel->itens()->create([
                'ferramenta_id' => $item['id'],
                'quantidade' => $item['quantidade'] ?? 1,
                'observacao' => $item['observacao'] ?? null,
                'status' => 'emprestado',
            ]);
        }

        return redirect()
            ->route('alugueis.show', $aluguel)
            ->with('success', 'Aluguel atualizado com sucesso!');
    }

    public function formDevolver(Aluguel $aluguel)
    {
        $user = Auth::user();
        
        // VERIFICA PERMISSÃO
        if ($user->role !== 'admin' && $aluguel->casa->setor_id != $user->setor_id) {
            return redirect()->route('alugueis.index')
                ->with('error', 'Você não tem permissão para devolver este aluguel!');
        }
        
        $responsaveis = User::where('role', 'responsavel_ferramentas')->get();
        return view('admin.alugueis.devolver', compact('aluguel', 'responsaveis'));
    }

    public function devolverPost(Request $request, Aluguel $aluguel)
    {
        $user = Auth::user();
        
        // VERIFICA PERMISSÃO
        if ($user->role !== 'admin' && $aluguel->casa->setor_id != $user->setor_id) {
            return redirect()->route('alugueis.index')
                ->with('error', 'Você não tem permissão para devolver este aluguel!');
        }
        
        // Atualiza o aluguel
        $aluguel->update([
            'data_devolucao' => now(),
            'status' => 'devolvido',
        ]);

        $dadosItens = $request->itens;

        foreach ($aluguel->itens as $index => $item) {
            $item->update(['status' => 'devolvido']);
            
            $estado = $dadosItens[$index]['estado'] ?? 'bom';
            
            $item->ferramenta->update([
                'estado' => $estado === 'manutencao' ? 'manutenção' : $estado
            ]);
            
            if (!empty($dadosItens[$index]['observacao'])) {
                $item->update([
                    'observacao_devolucao' => $dadosItens[$index]['observacao']
                ]);
            }
        }

        return redirect()
            ->route('alugueis.show', $aluguel->id)
            ->with('success', 'Aluguel devolvido com sucesso!');
    }
}