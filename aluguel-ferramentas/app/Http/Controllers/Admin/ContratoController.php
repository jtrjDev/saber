<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\ContratoItem;
use App\Models\ContratoHistorico;
use App\Models\Aluguel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContratoController extends Controller
{
    public function gerar(Aluguel $aluguel)
    {
        $user = Auth::user();
        
        // VERIFICA PERMISSÃO POR SETOR
        if ($user->role !== 'admin') {
            // Verifica se o aluguel pertence a uma casa do setor do usuário
            if ($aluguel->casa->setor_id != $user->setor_id) {
                return redirect()->route('alugueis.show', $aluguel)
                    ->with('error', 'Você não tem permissão para gerar contrato para este aluguel!');
            }
        }
        
        // Verifica se já existe contrato
        if ($aluguel->contrato) {
            return redirect()->route('contratos.show', $aluguel->contrato)
                ->with('warning', 'Este aluguel já possui um contrato!');
        }
        
        try {
            // 1) Criar número do contrato
            $ultimoContrato = Contrato::latest('id')->first();
            $proximoNumero = $ultimoContrato ? intval(substr($ultimoContrato->numero, -6)) + 1 : 1;
            $numero = now()->year . '-' . str_pad($proximoNumero, 6, '0', STR_PAD_LEFT);
            
            // 2) Criar contrato
            $contrato = Contrato::create([
                'aluguel_id' => $aluguel->id,
                'numero'     => $numero,
                'versao'     => 1,
            ]);
            
            // 3) Copiar itens do aluguel
            foreach ($aluguel->itens as $item) {
                ContratoItem::create([
                    'contrato_id' => $contrato->id,
                    'nome_ferramenta' => $item->ferramenta->nome,
                    'quantidade' => $item->quantidade,
                    'observacao' => $item->observacao ?? ''
                ]);
            }
            
            // 4) Gerar PDF
            $pdf = Pdf::loadView('admin.contratos.pdf', compact('contrato'));
            
            $path = "contratos/{$contrato->numero}.pdf";
            
            Storage::disk('public')->makeDirectory('contratos');
            Storage::disk('public')->put($path, $pdf->output());
            
            // Salvar caminho no banco
            $contrato->arquivo_pdf = $path;
            $contrato->save();
            
            // 5) Registrar histórico
            $contrato->historicos()->create([
                'acao' => 'Contrato criado',
                'detalhes' => 'Contrato gerado automaticamente com base no aluguel #' . $aluguel->id . ' pelo usuário ' . $user->name
            ]);
            
            return redirect()->route('contratos.show', $contrato)
                ->with('success', 'Contrato gerado com sucesso!');
                
        } catch (\Exception $e) {
            return redirect()->route('alugueis.show', $aluguel)
                ->with('error', 'Erro ao gerar contrato: ' . $e->getMessage());
        }
    }
    
    public function show(Contrato $contrato)
    {
        $user = Auth::user();
        
        // VERIFICA PERMISSÃO POR SETOR
        if ($user->role !== 'admin') {
            // Verifica se o aluguel do contrato pertence a uma casa do setor do usuário
            if ($contrato->aluguel->casa->setor_id != $user->setor_id) {
                return redirect()->route('alugueis.index')
                    ->with('error', 'Você não tem permissão para visualizar este contrato!');
            }
        }
        
        $contrato->load(['itens', 'aluguel.usuario', 'aluguel.casa', 'historicos']);
        
        return view('admin.contratos.show', compact('contrato'));
    }
    
    public function download(Contrato $contrato)
    {
        $user = Auth::user();
        
        // VERIFICA PERMISSÃO POR SETOR
        if ($user->role !== 'admin') {
            if ($contrato->aluguel->casa->setor_id != $user->setor_id) {
                return redirect()->route('alugueis.index')
                    ->with('error', 'Você não tem permissão para baixar este contrato!');
            }
        }
        
        if (!$contrato->arquivo_pdf || !Storage::disk('public')->exists($contrato->arquivo_pdf)) {
            // Regenera o PDF se não existir
            $pdf = Pdf::loadView('admin.contratos.pdf', compact('contrato'));
            $path = "contratos/{$contrato->numero}.pdf";
            Storage::disk('public')->put($path, $pdf->output());
            $contrato->arquivo_pdf = $path;
            $contrato->save();
        }
        
        return response()->download(storage_path('app/public/' . $contrato->arquivo_pdf), "contrato_{$contrato->numero}.pdf");
    }
    
    public function renovar(Contrato $contrato)
    {
        $user = Auth::user();
        
        // VERIFICA PERMISSÃO
        if ($user->role !== 'admin') {
            if ($contrato->aluguel->casa->setor_id != $user->setor_id) {
                return redirect()->route('alugueis.index')
                    ->with('error', 'Você não tem permissão para renovar este contrato!');
            }
        }
        
        // Incrementa versão
        $novaVersao = $contrato->versao + 1;
        
        // Atualiza número do contrato
        $partes = explode('-', $contrato->numero);
        $numero = $partes[0] . '-' . $partes[1] . '-v' . $novaVersao;
        
        // Cria novo contrato baseado no anterior
        $novoContrato = Contrato::create([
            'aluguel_id' => $contrato->aluguel_id,
            'numero' => $numero,
            'versao' => $novaVersao,
            'arquivo_pdf' => null
        ]);
        
        // Copia itens
        foreach ($contrato->itens as $item) {
            ContratoItem::create([
                'contrato_id' => $novoContrato->id,
                'nome_ferramenta' => $item->nome_ferramenta,
                'quantidade' => $item->quantidade,
                'observacao' => $item->observacao
            ]);
        }
        
        // Gera novo PDF
        $pdf = Pdf::loadView('admin.contratos.pdf', compact('novoContrato'));
        $path = "contratos/{$novoContrato->numero}.pdf";
        Storage::disk('public')->put($path, $pdf->output());
        $novoContrato->arquivo_pdf = $path;
        $novoContrato->save();
        
        // Registra histórico no contrato original
        $contrato->historicos()->create([
            'acao' => 'Contrato renovado',
            'detalhes' => "Contrato renovado para versão {$novaVersao} por {$user->name}"
        ]);
        
        // Histórico no novo contrato
        $novoContrato->historicos()->create([
            'acao' => 'Contrato criado',
            'detalhes' => "Contrato renovado a partir da versão anterior por {$user->name}"
        ]);
        
        return redirect()->route('contratos.show', $novoContrato)
            ->with('success', 'Contrato renovado com sucesso!');
    }
}