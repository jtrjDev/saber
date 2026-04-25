<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SetorPermissionService
{
    /**
     * Verifica se o usuário tem acesso a um recurso específico
     */
    public function userHasAccessTo(Model $model): bool
    {
        $user = Auth::user();
        
        if (!$user || $user->role === 'admin') {
            return true;
        }
        
        // Verifica se o model tem setor_id
        if (property_exists($model, 'setor_id') || method_exists($model, 'getSetorIdAttribute')) {
            return $model->setor_id == $user->setor_id;
        }
        
        // Se o model tem relação com setor
        if (method_exists($model, 'setor')) {
            return $model->setor_id == $user->setor_id;
        }
        
        return false;
    }
    
    /**
     * Retorna o ID do setor do usuário ou null se for admin
     */
    public function getSetorIdUsuario(): ?int
    {
        $user = Auth::user();
        
        if (!$user) {
            return null;
        }
        
        return $user->role === 'admin' ? null : $user->setor_id;
    }
    
    /**
     * Valida se o usuario pode operar em um determinado setor_id
     */
    public function canOperateInSetor(?int $setorId): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        if ($user->role === 'admin') {
            return true;
        }
        
        return $setorId == $user->setor_id;
    }
}