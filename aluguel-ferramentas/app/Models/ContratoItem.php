<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContratoItem extends Model
{
   protected $table = 'contrato_items';


    protected $fillable = [
        'contrato_id', 'nome_ferramenta', 'quantidade', 'observacao'
    ];

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }
}
