<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setor extends Model
{
    protected $table = 'setores';
    protected $primaryKey = 'id';     // <- IMPORTANTE

    protected $fillable = ['nome'];

    public function getRouteKeyName()
    {
        return 'id';                  // <- ESSENCIAL
    }

    public function casas()
    {
        return $this->hasMany(Casa::class, 'setor_id', 'id');
    }

    public function ferramentas()
    {
        return $this->hasMany(Ferramenta::class, 'setor_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'setor_id', 'id');
    }
}
