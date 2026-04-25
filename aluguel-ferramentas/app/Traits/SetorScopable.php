<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait SetorScopable
{
    /**
     * Escopo global para filtrar por setor do usuário
     */
    public function scopeDoSetorDoUsuario($query)
    {
        $user = auth()->user();
        
        // Se não tem usuário logado ou é admin, retorna tudo
        if (!$user || $user->role === 'admin') {
            return $query;
        }
        
        // Se a model tem setor_id, aplica o filtro
        if ($this->hasSetorId()) {
            return $query->where('setor_id', $user->setor_id);
        }
        
        return $query;
    }
    
    /**
     * Verifica se a model possui o campo setor_id
     */
    protected function hasSetorId()
    {
        return in_array('setor_id', $this->getFillable()) || 
               $this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), 'setor_id');
    }
}