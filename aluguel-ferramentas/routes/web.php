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
// PERFIL DO USUÁRIO (logado)
// =========================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =========================================================
// ROTAS ADMIN + RESPONSÁVEL FERRAMENTAS
// =========================================================
Route::middleware(['auth', 'role:admin,responsavel_ferramentas'])
    ->prefix('admin')
    ->group(function () {

        // -----------------------------
        // ALMOXARIFADO
        // -----------------------------
        Route::get('/almoxarifado',
            [AlmoxarifadoController::class, 'index']
        )->name('almox.dashboard');

        // -----------------------------
        // FERRAMENTAS (CRUD COMPLETO)
        // -----------------------------
        Route::resource('ferramentas', FerramentaController::class);

        // -----------------------------
        // ALUGUÉIS (CRUD COMPLETO)
        // -----------------------------
        Route::resource('alugueis', AluguelController::class)
            ->parameters(['alugueis' => 'aluguel']);

        // -----------------------------
        // FORMULÁRIO DE DEVOLUÇÃO (GET / POST)
        // -----------------------------
        Route::get('/alugueis/{aluguel}/devolver',
            [AluguelController::class, 'formDevolver']
        )->name('alugueis.devolver');

        Route::post('/alugueis/{aluguel}/devolver',
            [AluguelController::class, 'devolverPost']
        )->name('alugueis.devolver.post');

        // -----------------------------
        // ITENS DO ALUGUEL (ações)
        // -----------------------------
        Route::post('alugueis/item/{aluguelItem}/devolver',
            [AluguelItemController::class, 'devolver']
        )->name('alugueis.item.devolver');

        Route::post('alugueis/item/{aluguelItem}/renovar',
            [AluguelItemController::class, 'renovar']
        )->name('alugueis.item.renovar');

        Route::post('alugueis/item/{aluguelItem}/perdido',
            [AluguelItemController::class, 'perdido']
        )->name('alugueis.item.perdido');
    });

// =========================================================
// ROTAS APENAS ADMIN
// =========================================================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        // -----------------------------
        // SETORES
        // -----------------------------
        Route::resource('setores', SetorController::class)
            ->parameters(['setores' => 'setor']);

        // -----------------------------
        // CASAS
        // -----------------------------
        Route::resource('casas', CasaController::class);

        // -----------------------------
        // USUÁRIOS
        // -----------------------------
        Route::resource('usuarios', UserController::class)
            ->parameters(['usuarios' => 'usuario']);

        // -----------------------------
        // CONTRATOS
        // -----------------------------
        Route::get('contrato/gerar/{aluguel}',
            [ContratoController::class, 'gerar']
        )->name('contrato.gerar');

        Route::get('contratos/{contrato}',
            [ContratoController::class, 'show']
        )->name('contratos.show');
    });

// =========================================================
// AUTH
// =========================================================
require __DIR__.'/auth.php';
