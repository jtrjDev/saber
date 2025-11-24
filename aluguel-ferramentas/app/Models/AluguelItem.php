<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AluguelItem extends Model
{
    protected $table = 'aluguel_itens'; // opcional, mas recomendado

    protected $fillable = [
        'aluguel_id',
        'ferramenta_id',
        'observacao',
        'quantidade'
    ];

    public function ferramenta() {
        return $this->belongsTo(Ferramenta::class);
    }
    public function aluguel()
    {
        return $this->belongsTo(Aluguel::class, 'aluguel_id', 'id');
    }

}
