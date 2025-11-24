<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ferramenta extends Model
{
    protected $fillable = [
        'nome',
        'foto',
        'estado',
        'valor_compra',
        'descricao',
        'setor_id',
    ];

    public function setor()
    {
        return $this->belongsTo(Setor::class);
    }
}
