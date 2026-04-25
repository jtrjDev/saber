<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SetorScopable;

class ContratoItem extends Model
{
    use SetorScopable;  // ADICIONE ISSO
    protected $table = 'contrato_items';


    protected $fillable = [
        'contrato_id', 'nome_ferramenta', 'quantidade', 'observacao'
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }
}
