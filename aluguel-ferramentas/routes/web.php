<?php

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
use Illuminate\Support\Facades\Route;


// -----------------------------
// Página inicial
// -----------------------------
Route::get('/', function () {
    return view('welcome');
});


// Dashboard único — somente admin e responsável_ferramentas
Route::middleware(['auth', 'role:admin,responsavel_ferramentas'])
    ->get('/dashboard', [DashboardGeralController::class, 'index'])
    ->name('dashboard');


// -----------------------------
// Rotas: Perfil do usuário
// -----------------------------
Route::middleware('auth')->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});


// -----------------------------
// ALMOXARIFADO (Admin + Responsável Ferramentas)
// -----------------------------
Route::middleware(['auth', 'role:responsavel_ferramentas,admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/almoxarifado', [AlmoxarifadoController::class, 'index'])
            ->name('almox.dashboard');
    });


// -----------------------------
// ROTAS ADMINISTRATIVAS (APENAS ADMIN)
// -----------------------------
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        Route::resource('setores', SetorController::class)->parameters(['setores' => 'setor']);

        Route::resource('casas', CasaController::class);

        Route::resource('usuarios', UserController::class)->parameters(['usuarios' => 'usuario']);

        Route::resource('ferramentas', FerramentaController::class);

        Route::resource('alugueis', AluguelController::class)
            ->parameters(['alugueis' => 'aluguel']);

        Route::post('alugueis/{aluguel}/devolver', [AluguelController::class, 'devolver'])
            ->name('alugueis.devolver');

        // Contratos
        Route::get('contrato/gerar/{aluguel}', [ContratoController::class, 'gerar'])
            ->name('contrato.gerar');

        Route::get('contratos/{contrato}', [ContratoController::class, 'show'])
            ->name('contratos.show');

        // Item do aluguel
        Route::post('alugueis/item/{aluguelItem}/devolver', [AluguelItemController::class, 'devolver'])
            ->name('alugueis.item.devolver');

        Route::post('alugueis/item/{aluguelItem}/renovar', [AluguelItemController::class, 'renovar'])
            ->name('alugueis.item.renovar');

        Route::post('alugueis/item/{aluguelItem}/perdido', [AluguelItemController::class, 'perdido'])
            ->name('alugueis.item.perdido');
    });


// -----------------------------
// Auth
// -----------------------------
require __DIR__.'/auth.php';
