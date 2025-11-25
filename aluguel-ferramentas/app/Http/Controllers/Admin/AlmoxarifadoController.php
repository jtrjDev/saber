<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ferramenta;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AlmoxarifadoController extends Controller
{
    public function index(Request $request)
    {
        // Filtros
        $statusFiltro = $request->status ?? null;

        // Ferramentas com o último aluguel (se houver)
        $ferramentas = Ferramenta::with(['itens.aluguel.usuario', 'itens.aluguel.casa'])
            ->get()
            ->map(function($f) {
                $f->ultimo_aluguel = $f->itens
                    ->whereIn('status', ['emprestado', 'renovado', 'parcial'])
                    ->sortByDesc('created_at')
                    ->first();

                return $f;
            });

        // Estatísticas
        $stats = [
            'disponiveis' => $ferramentas->where('ultimo_aluguel', null)->count(),
            'emprestadas' => $ferramentas->where('ultimo_aluguel', '!=', null)->count(),
            'atrasadas' => $ferramentas->filter(function($f){
                if(!$f->ultimo_aluguel) return false;
                return now()->gt(Carbon::parse($f->ultimo_aluguel->aluguel->data_prevista));
            })->count(),
        ];

        return view('admin.almoxarifado.dashboard', compact('ferramentas', 'stats'));
    }
}
