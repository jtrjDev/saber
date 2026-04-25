<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SetorScopable;

class Ferramenta extends Model
{
     use SetorScopable;  // ADICIONE ISSO
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

    public function itens()
{
    return $this->hasMany(\App\Models\AluguelItem::class, 'ferramenta_id');
}

}
