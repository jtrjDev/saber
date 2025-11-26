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
        $ferramentas = Ferramenta::whereDoesntHave('itens.aluguel', function($q) {
            $q->whereNull('data_devolucao'); // ainda não devolveu
        })->orderBy('nome')->get();


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
            'ferramentas.*.quantidade' => 'required|integer|min:1',
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
                'quantidade' => $item['quantidade'],
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
        $query->whereDoesntHave('itens.aluguel', function($q) {
            $q->whereNull('data_devolucao');
        })
        ->orWhereIn('id', $itensAtuais); // permitir ferramentas já usadas no aluguel
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
        'ferramentas.*.quantidade' => 'required|integer|min:1',
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
            'quantidade' => $item['quantidade'],
            'observacao' => $item['observacao'] ?? null,
        ]);
    }

    return redirect()
        ->route('alugueis.show', $aluguel)
        ->with('success', 'Aluguel atualizado com sucesso!');
}



    public function devolver(Aluguel $aluguel)
    {
        $aluguel->update([
            'data_devolucao_real' => now(),
            'status' => 'devolvido'
        ]);

        return back()->with('success','Ferramentas devolvidas!');
    }
}
