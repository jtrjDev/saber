<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SetorScopable;

class ContratoHistorico extends Model
{
        use SetorScopable;  // ADICIONE ISSO

    protected $fillable = [
        'contrato_id', 'acao', 'detalhes'
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }
}
