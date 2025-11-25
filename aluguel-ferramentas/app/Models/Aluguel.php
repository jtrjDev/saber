<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aluguel extends Model
{
    protected $table = 'alugueis';

    protected $fillable = [
        'user_id',
        'responsavel_id',
        'casa_id',
        'data_retirada',
        'data_prevista',
        'data_devolucao',
        'status',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function casa()
    {
        return $this->belongsTo(Casa::class, 'casa_id');
    }

    public function itens()
    {
        return $this->hasMany(AluguelItem::class);
    }

    public function contrato()
    {
        return $this->hasOne(Contrato::class);
    }
    public function getStatusAttribute($value)
    {
        $statusOriginal = $this->attributes['status'] ?? $value;

        // Se ainda não devolveu e está atrasado
        if ($statusOriginal !== 'devolvido' && now()->gt($this->data_prevista)) {
            return 'atrasado';
        }

        return $statusOriginal;
    }



}
