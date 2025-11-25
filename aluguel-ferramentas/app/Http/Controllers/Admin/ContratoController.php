<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\ContratoItem;
use App\Models\ContratoHistorico;
use App\Models\Aluguel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ContratoController extends Controller
{
    public function gerar(Aluguel $aluguel)
    {
        // 1) Criar número do contrato 2025-000001
        $numero = now()->year . '-' . str_pad(Contrato::count() + 1, 6, '0', STR_PAD_LEFT);

        // 2) Criar contrato
        $contrato = Contrato::create([
            'aluguel_id' => $aluguel->id,
            'numero'     => $numero,
        ]);

        // 3) Copiar itens do aluguel
        foreach ($aluguel->itens as $item) {
            ContratoItem::create([
                'contrato_id' => $contrato->id,
                'nome_ferramenta' => $item->ferramenta->nome,
                'quantidade' => $item->quantidade,
                'observacao' => $item->observacao
            ]);
        }

        // 4) Gerar PDF
        $pdf = Pdf::loadView('admin.contratos.pdf', compact('contrato'));

        $path = "contratos/{$contrato->numero}.pdf";

        \Storage::disk('public')->makeDirectory('contratos');
        \Storage::disk('public')->put($path, $pdf->output());

        // Salvar caminho no banco
        $contrato->arquivo_pdf = $path;
        $contrato->save();

        // 5) Registrar histórico
        $contrato->historicos()->create([
            'acao' => 'Contrato criado',
            'detalhes' => 'Contrato gerado automaticamente com base no aluguel #' . $aluguel->id
        ]);

        return redirect()->route('contratos.show', $contrato)
            ->with('success', 'Contrato gerado com sucesso!');
    }

    public function show(Contrato $contrato)
    {
        $contrato->load(['itens', 'aluguel.usuario', 'historicos']);

        return view('admin.contratos.show', compact('contrato'));
    }
}
