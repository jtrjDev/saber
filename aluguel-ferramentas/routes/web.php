<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\SetorController;
use App\Http\Controllers\Admin\CasaController;
use App\Http\Controllers\Admin\FerramentaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AluguelController;
use App\Http\Controllers\Admin\ContratoController;
use App\Http\Controllers\Admin\AluguelItemController;
use App\Http\Controllers\Admin\AlmoxarifadoController;
use App\Http\Controllers\Admin\DashboardGeralController;


// =========================================================
// Página inicial
// =========================================================
Route::get('/', function () {
    return view('welcome');
});


// =========================================================
// DASHBOARD (Admin + Responsável Ferramentas)
// =========================================================
Route::middleware(['auth', 'role:admin,responsavel_ferramentas'])
    ->get('/dashboard', [DashboardGeralController::class, 'index'])
    ->name('dashboard');


// =========================================================
// FORMULÁRIO DE DEVOLUÇÃO — (GET E POST) — Admin e Responsável
// =========================================================
Route::middleware(['auth', 'role:admin,responsavel_ferramentas'])
    ->prefix('admin')
    ->group(function () {
        
        // Exibe tela de devolução
        Route::get('/alugueis/{aluguel}/devolver', 
            [AluguelController::class, 'formDevolver']
        )->name('alugueis.devolver');

        // Processa devolução
        Route::post('/alugueis/{aluguel}/devolver', 
            [AluguelController::class, 'devolverPost']
        )->name('alugueis.devolver.post');
    });


// =========================================================
// PERFIL DO USUÁRIO (logado)
// =========================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});


// =========================================================
// ALMOXARIFADO (Admin + Responsável Ferramentas)
// =========================================================
Route::middleware(['auth', 'role:responsavel_ferramentas,admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/almoxarifado', 
            [AlmoxarifadoController::class, 'index']
        )->name('almox.dashboard');

    });


// =========================================================
// ROTAS ADMINISTRATIVAS (APENAS ADMIN)
// =========================================================

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        // Setores
        Route::resource('setores', SetorController::class)
            ->parameters(['setores' => 'setor']);

        // Casas
        Route::resource('casas', CasaController::class);

        // Usuários
        Route::resource('usuarios', UserController::class)
            ->parameters(['usuarios' => 'usuario']);

        // Ferramentas
        Route::resource('ferramentas', FerramentaController::class);

        // Aluguéis
        Route::resource('alugueis', AluguelController::class)
            ->parameters(['alugueis' => 'aluguel']);


        // =====================================================
        // ITENS DO ALUGUEL → Ações individuais
        // =====================================================

        Route::post('alugueis/item/{aluguelItem}/devolver',
            [AluguelItemController::class, 'devolver']
        )->name('alugueis.item.devolver');

        Route::post('alugueis/item/{aluguelItem}/renovar',
            [AluguelItemController::class, 'renovar']
        )->name('alugueis.item.renovar');

        Route::post('alugueis/item/{aluguelItem}/perdido',
            [AluguelItemController::class, 'perdido']
        )->name('alugueis.item.perdido');


        // =====================================================
        // CONTRATOS
        // =====================================================

        Route::get('contrato/gerar/{aluguel}',
            [ContratoController::class, 'gerar']
        )->name('contrato.gerar');

        Route::get('contratos/{contrato}',
            [ContratoController::class, 'show']
        )->name('contratos.show');

    });


// =========================================================
// AUTH FILE
// =========================================================
require __DIR__.'/auth.php';
