<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SetorScopable;

class AluguelItem extends Model
{
    use SetorScopable;  // ADICIONE ISSO
    protected $table = 'aluguel_itens'; // opcional, mas recomendado

 protected $fillable = [
        'aluguel_id',
        'ferramenta_id',
        'quantidade',
        'status',
        'data_devolucao_item',
        'observacao',
        'observacao_devolucao',
    ];


    public function ferramenta() {
        return $this->belongsTo(Ferramenta::class);
    }
    public function aluguel()
    {
        return $this->belongsTo(Aluguel::class, 'aluguel_id', 'id');
    }
    public function isDevolvido()
    {
        return $this->status === 'devolvido';
    }

    public function isEmprestado()
    {
        return $this->status === 'emprestado';
    }


}
