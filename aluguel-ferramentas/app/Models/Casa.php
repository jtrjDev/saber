<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SetorScopable;

class Casa extends Model
{
     use SetorScopable;  // ADICIONE ISSO
    protected $table = 'casas';   // <- importante
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'nome',
        'setor_id',
    ];

   public function setor()
{
    return $this->belongsTo(Setor::class, 'setor_id', 'id');
}

    public function alugueis()
{
    return $this->hasMany(\App\Models\Aluguel::class);
}


}
