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
use Illuminate\Support\Facades\Hash;


class AluguelController extends Controller
{


    private function calcularDataPrevista($dataRetirada, $periodo)
{
    $data = Carbon::parse($dataRetirada);

    if (str_contains($periodo, "dia")) {
        $numero = (int) filter_var($periodo, FILTER_SANITIZE_NUMBER_INT);
        return $data->addDays($numero);
    }

    if (str_contains($periodo, "semana")) {
        $numero = (int) filter_var($periodo, FILTER_SANITIZE_NUMBER_INT);
        return $data->addWeeks($numero);
    }

    if (str_contains($periodo, "mes")) {
        $numero = (int) filter_var($periodo, FILTER_SANITIZE_NUMBER_INT);
        return $data->addMonths($numero);
    }

    if (str_contains($periodo, "ano")) {
        return $data->addYear();
    }

    return $data; // fallback
}

    public function index(Request $request)
    {
        $query = Aluguel::with(['casa','usuario','responsavel'])->orderBy('id','desc');

        if ($request->filled('search')) {
            $query->whereHas('usuario', function($q) use ($request) {
                $q->where('name','like','%'.$request->search.'%');
            });
        }

        $alugueis = $query->paginate(10);

        return view('admin.alugueis.index', compact('alugueis'));
    }

    public function create()
    {
        $casas = Casa::orderBy('nome')->get();
        $responsaveis = User::where('role','responsavel_ferramentas')->get();
        $usuarios = User::where('role','membro')
                        ->orWhere('role','gestor_obra')
                        ->get();
        $ferramentas = Ferramenta::whereIn('estado', ['boa', 'nova'])
    ->whereDoesntHave('itens', function($q){
        // Se existe item vinculado a aluguel que não devolveu → não pode
        $q->whereHas('aluguel', function($sub){
            $sub->whereNull('data_devolucao');
        });
    })
    ->orderBy('nome')
    ->get();




        return view('admin.alugueis.create', compact('casas','responsaveis','usuarios','ferramentas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'casa_id' => 'required|exists:casas,id',
            'responsavel_id' => 'required|exists:users,id',
            'data_retirada' => 'required|date',
            'alugar_por' => 'required|string',
            'ferramentas' => 'required|array|min:1',
            'ferramentas.*.id' => 'required|exists:ferramentas,id',
            
        ]);

        // 1) Criar usuário caso seja "Outro"
        if ($request->user_id === "outro") {

            $nome = trim($request->novo_usuario);

            $novoUser = User::create([
                'name' => $nome,
                'email' => strtolower(str_replace(' ', '', $nome))."@temporario.local",
                'password' => Hash::make('123456'),
                'role' => 'membro'
            ]);

            $userId = $novoUser->id;

        } else {
            $userId = $request->user_id;
        }

        // 2) Calcular data prevista
        $dataPrevista = $this->calcularDataPrevista($request->data_retirada, $request->alugar_por);

        // 3) Criar o aluguel
        $aluguel = Aluguel::create([
            'casa_id' => $request->casa_id,
            'user_id' => $userId,
            'responsavel_id' => $request->responsavel_id,
            'data_retirada' => $request->data_retirada,
            'data_prevista' => $dataPrevista,
            'status' => 'ativo',
        ]);

        // 4) Criar itens do aluguel
        foreach ($request->ferramentas as $item) {
            AluguelItem::create([
                'aluguel_id' => $aluguel->id,
                'ferramenta_id' => $item['id'],
                'quantidade' => $item->quantidade ?? 1,
                'observacao' => $item['observacao'] ?? null,
            ]);
        }

        // 5) Redirecionar para o show
        return redirect()->route('alugueis.show', $aluguel->id)
                        ->with('success', 'Aluguel criado com sucesso!');
    }



public function show(Aluguel $aluguel)
{
    // Carregar os relacionamentos necessários
    $aluguel->load(['casa', 'usuario', 'responsavel', 'itens.ferramenta']);
    return view('admin.alugueis.show', compact('aluguel'));
}



    public function edit(Aluguel $aluguel)
{
    $aluguel->load(['itens', 'itens.ferramenta']);

    $casas = Casa::orderBy('nome')->get();
    $responsaveis = User::where('role','responsavel_ferramentas')->get();
    $usuarios = User::whereIn('role', ['membro', 'gestor_obra'])->get();
    $itensAtuais = $aluguel->itens->pluck('ferramenta_id')->toArray();

   $ferramentas = Ferramenta::where(function($query) use ($itensAtuais) {
        $query
            ->whereIn('estado', ['boa', 'nova'])
            ->whereDoesntHave('itens', function($q){
                $q->whereHas('aluguel', function($sub){
                    $sub->whereNull('data_devolucao');
                });
            })
            ->orWhereIn('id', $itensAtuais); // permitir as que já pertencem ao aluguel
    })
    ->orderBy('nome')
    ->get();



    return view('admin.alugueis.edit', compact(
        'aluguel', 'casas', 'responsaveis', 'usuarios', 'ferramentas'
    ));
}

public function update(Request $request, Aluguel $aluguel)
{
    $request->validate([
        'casa_id' => 'required|exists:casas,id',
        'user_id' => 'required',
        'responsavel_id' => 'required|exists:users,id',
        'data_retirada' => 'required|date',
        'alugar_por' => 'required|string',

        // itens
        'ferramentas.*.id' => 'required|exists:ferramentas,id',
        
    ]);

    // Atualiza dados gerais
    $aluguel->update([
        'casa_id' => $request->casa_id,
        'user_id' => $request->user_id,
        'responsavel_id' => $request->responsavel_id,
        'data_retirada' => $request->data_retirada,
        'data_prevista' => $this->calcularDataPrevista($request->data_retirada, $request->alugar_por),
        'status' => $aluguel->status, // não alteramos aqui
    ]);

    // 🔥 Atualiza itens
    $aluguel->itens()->delete(); // limpa tudo e insere de novo

    foreach ($request->ferramentas as $item) {
        $aluguel->itens()->create([
            'ferramenta_id' => $item['id'],
            'quantidade' => $item->quantidade ?? 1,
            'observacao' => $item['observacao'] ?? null,
        ]);
    }

    return redirect()
        ->route('alugueis.show', $aluguel)
        ->with('success', 'Aluguel atualizado com sucesso!');
}

public function formDevolver(Aluguel $aluguel)
{
    $responsaveis = User::where('role', 'responsavel_ferramentas')->get();
    return view('admin.alugueis.devolver', compact('aluguel','responsaveis'));
}

public function devolverPost(Request $request, Aluguel $aluguel)
{
    // Atualiza o aluguel
    $aluguel->update([
        'data_devolucao' => now(),
        'status' => 'devolvido',
    ]);

    // Pega itens enviados
    $dadosItens = $request->itens; // <- IMPORTANTÍSSIMO

    // Atualiza TODOS os itens
    foreach ($aluguel->itens as $index => $item) {

        $item->update(['status' => 'devolvido']);

        // Verifica o estado informado pelo usuário
        $estado = $dadosItens[$index]['estado'] ?? 'bom';

        // Atualiza estado da ferramenta corretamente
        $item->ferramenta->update([
            'estado' => $estado === 'manutencao' ? 'manutenção' : $estado
        ]);

        // Observação da manutenção
        if (!empty($dadosItens[$index]['observacao'])) {
            $item->update([
                'observacao' => $dadosItens[$index]['observacao']
            ]);
        }
    }

    return redirect()
        ->route('alugueis.show', $aluguel->id)
        ->with('success', 'Aluguel devolvido com sucesso!');
}




 
}
