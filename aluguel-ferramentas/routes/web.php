<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\SetorController;
use App\Http\Controllers\Admin\CasaController;
use App\Http\Controllers\Admin\FerramentaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AluguelController;
use Illuminate\Support\Facades\Route;

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
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        Route::resource('setores', SetorController::class)
    ->parameters([
        'setores' => 'setor'
    ]);

        Route::resource('casas', CasaController::class);
        Route::resource('usuarios', UserController::class)
            ->parameters(['usuarios' => 'usuario']);
    
    });
    Route::resource('ferramentas', FerramentaController::class);
    Route::resource('alugueis', AluguelController::class);

Route::post('alugueis/{aluguel}/devolver', [AluguelController::class, 'devolver'])
    ->name('alugueis.devolver');

require __DIR__.'/auth.php';
