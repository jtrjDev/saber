<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SetorScopable;

class Contrato extends Model
{
    use SetorScopable;  // ADICIONE ISSO
    protected $fillable = [
        'numero', 'aluguel_id', 'arquivo_pdf', 'versao', 'data_assinatura'
    ];

    public function aluguel()
    {
        return $this->belongsTo(Aluguel::class);
    }

    public function itens()
    {
        return $this->hasMany(ContratoItem::class);
    }

    public function historicos()
    {
        return $this->hasMany(ContratoHistorico::class);
    }
}
