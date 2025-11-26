<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ferramenta;
use App\Models\Aluguel;
use App\Models\User;
use App\Models\Casa;
use Carbon\Carbon;

class DashboardGeralController extends Controller
{
    public function index()
    {
        $hoje = Carbon::today();
        $amanha = Carbon::tomorrow();

        // CONTADORES
        $totalFerramentas = Ferramenta::count();

        $disponiveis = Ferramenta::whereDoesntHave('itens', function($q){
            $q->whereIn('status', ['emprestado', 'renovado']);
        })->count();

        $emprestadas = Ferramenta::whereHas('itens', function($q){
            $q->whereIn('status', ['emprestado', 'renovado']);
        })->count();

        $atrasadas = Aluguel::whereNull('data_devolucao')
            ->whereDate('data_prevista', '<', $hoje)
            ->count();

        $devolver48 = Aluguel::whereNull('data_devolucao')
            ->whereBetween('data_prevista', [$hoje, $hoje->copy()->addDays(2)])
            ->count();

        $usuariosAtivos = User::whereHas('alugueis')->count();

        // Totais por casa
        $totaisPorCasa = Casa::withCount([
            'alugueis as total_emprestadas' => function ($q) {
                $q->whereNull('data_devolucao');
            }
        ])->get();

        // filtros
        $casaFiltro = request('casa');
        $statusFiltro = request('status');

        // Ferramentas filtradas
        $ferramentas = Ferramenta::with(['itens.aluguel.usuario', 'itens.aluguel.casa'])
            ->when($casaFiltro, function($q) use ($casaFiltro) {
                $q->whereHas('itens.aluguel', function($sub) use ($casaFiltro) {
                    $sub->where('casa_id', $casaFiltro);
                });
            })
            ->when($statusFiltro === 'atrasado', function($q) use ($hoje) {
                $q->whereHas('itens.aluguel', function($sub) use ($hoje) {
                    $sub->whereNull('data_devolucao')
                        ->where('data_prevista', '<', $hoje);
                });
            })
            ->when($statusFiltro === 'normal', function($q) use ($hoje) {
                $q->whereHas('itens.aluguel', function($sub) use ($hoje) {
                    $sub->whereNull('data_devolucao')
                        ->where('data_prevista', '>=', $hoje);
                });
            })
            ->get();

        return view('admin.dashboard-geral', compact(
            'totalFerramentas',
            'disponiveis',
            'emprestadas',
            'atrasadas',
            'devolver48',
            'usuariosAtivos',
            'ferramentas',
            'totaisPorCasa',
            'casaFiltro',
            'statusFiltro'
        ));
    }
}
