<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AluguelItem;
use App\Models\Aluguel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AluguelItemController extends Controller
{
    public function devolver(AluguelItem $aluguelItem, Request $request)
{
    // Atualiza o item
    $aluguelItem->update([
        'status' => 'devolvido',
        'data_devolucao_item' => now(),
        'observacao_devolucao' => $request->observacao
    ]);

    // Recalcular status do aluguel
    $aluguel = $aluguelItem->aluguel;

    $total = $aluguel->itens()->count();
    $devolvidos = $aluguel->itens()->where('status', 'devolvido')->count();
    $emprestados = $aluguel->itens()->where('status', 'emprestado')->count();

    if ($devolvidos === $total) {
        // tudo devolvido
        $aluguel->update([
            'status' => 'devolvido',
            'data_devolucao' => now(),
        ]);
    } elseif ($devolvidos > 0 && $emprestados > 0) {
        // devolução parcial
        $aluguel->update([
            'status' => 'parcial'
        ]);
    } else {
        // ainda tudo emprestado
        $aluguel->update([
            'status' => 'emprestado'
        ]);
    }

    return back()->with('success', 'Item devolvido!');
}




    public function renovar(AluguelItem $item, Request $request)
    {
        $dias = $request->dias ?? 3;

        $novaData = Carbon::parse($item->aluguel->data_prevista)
            ->addDays($dias);

        // Atualiza APENAS o item (não muda os outros)
        $item->update([
            'status' => 'renovado',
            'observacao_devolucao' => 'Renovado por ' . $dias . ' dias',
        ]);

        // Atualiza data prevista geral DO ALUGUEL
        $item->aluguel->update([
            'data_prevista' => $novaData
        ]);

        return back()->with('success', 'Item renovado!');
    }

    public function perdido(AluguelItem $item)
    {
        $item->update([
            'status' => 'perdido',
            'data_devolucao_item' => now()
        ]);

        return back()->with('success', 'Item marcado como perdido.');
    }
}
