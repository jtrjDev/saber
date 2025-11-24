<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Casa extends Model
{
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

}
