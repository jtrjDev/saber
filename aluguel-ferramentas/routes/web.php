<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\SetorController;
use App\Http\Controllers\Admin\CasaController;
use App\Http\Controllers\Admin\FerramentaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AluguelController;
use App\Http\Controllers\Admin\ContratoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AluguelItemController;


Route::get('/', function () {
    return view('welcome');
});

// Dashboard acessível para qualquer usuário logado
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Perfil do usuário
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rotas administrativas (somente ADMIN)
// Rotas administrativas (somente ADMIN)
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        Route::resource('setores', SetorController::class)
            ->parameters(['setores' => 'setor']);

        Route::resource('casas', CasaController::class);
        Route::resource('usuarios', UserController::class)
            ->parameters(['usuarios' => 'usuario']);

        // Ferramentas dentro de admin
        Route::resource('ferramentas', FerramentaController::class);

        Route::resource('alugueis', AluguelController::class)
                    ->parameters(['alugueis' => 'aluguel']);

        Route::post('alugueis/{aluguel}/devolver', 
            [AluguelController::class, 'devolver']
        )->name('alugueis.devolver');

         Route::get('contrato/gerar/{aluguel}', [ContratoController::class, 'gerar'])
        ->name('contrato.gerar');

    Route::get('contratos/{contrato}', [ContratoController::class, 'show'])
        ->name('contratos.show');

       Route::post('alugueis/item/{aluguelItem}/devolver', [AluguelItemController::class, 'devolver'])
    ->name('alugueis.item.devolver');

Route::post('alugueis/item/{aluguelItem}/renovar', [AluguelItemController::class, 'renovar'])
    ->name('alugueis.item.renovar');

Route::post('alugueis/item/{aluguelItem}/perdido', [AluguelItemController::class, 'perdido'])
    ->name('alugueis.item.perdido');


});



require __DIR__.'/auth.php';
