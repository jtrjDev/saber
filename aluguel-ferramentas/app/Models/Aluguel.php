<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SetorScopable;
use Carbon\Carbon;

class Aluguel extends Model
{
    use SetorScopable;
    
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

    protected $dates = [
        'data_retirada',
        'data_prevista',
        'data_devolucao',
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

        if ($statusOriginal !== 'devolvido' && now()->gt($this->data_prevista)) {
            return 'atrasado';
        }

        return $statusOriginal;
    }
    
    /**
     * Calcula o período do aluguel de forma inteligente
     */
    public function getPeriodoAluguelAttribute()
    {
        $dataRetirada = Carbon::parse($this->data_retirada);
        $dataPrevista = Carbon::parse($this->data_prevista);
        
        $dias = $dataRetirada->diffInDays($dataPrevista);
        $meses = $dataRetirada->diffInMonths($dataPrevista);
        $semanas = floor($dias / 7);
        $diasRestantes = $dias % 7;
        
        // Verifica se é exato em meses
        if ($meses > 0 && $dataRetirada->addMonths($meses)->format('Y-m-d') === $dataPrevista->format('Y-m-d')) {
            if ($meses == 1) return '1 mes';
            if ($meses == 2) return '2 meses';
            if ($meses == 3) return '3 meses';
            if ($meses == 4) return '4 meses';
            if ($meses == 6) return '6 meses';
            if ($meses == 12) return '1 ano';
            return $meses . ' meses';
        }
        
        // Verifica se é exato em semanas
        if ($dias % 7 == 0 && $semanas > 0) {
            if ($semanas == 1) return '1 semana';
            if ($semanas == 2) return '2 semanas';
            if ($semanas == 3) return '3 semanas';
            return $semanas . ' semanas';
        }
        
        // Verifica períodos específicos
        if ($dias == 1) return '1 dia';
        if ($dias == 2) return '2 dias';
        if ($dias == 3) return '3 dias';
        if ($dias == 4) return '4 dias';
        if ($dias == 5) return '5 dias';
        
        // Se não for exato, retorna em dias
        return $dias . ' dias';
    }
    
    /**
     * Retorna o valor para o select (mapeamento)
     */
    public function getPeriodoSelectAttribute()
    {
        $periodo = $this->periodo_aluguel;
        
        $mapa = [
            '1 dia' => '1 dia',
            '2 dias' => '2 dias',
            '3 dias' => '3 dias',
            '4 dias' => '4 dias',
            '5 dias' => '5 dias',
            '1 semana' => '1 semana',
            '2 semanas' => '2 semanas',
            '3 semanas' => '3 semanas',
            '1 mes' => '1 mes',
            '2 meses' => '2 meses',
            '3 meses' => '3 meses',
            '4 meses' => '4 meses',
            '6 meses' => '6 meses',
            '1 ano' => '1 ano',
        ];
        
        // Tenta encontrar no mapa, se não encontrar, usa o período calculado
        if (isset($mapa[$periodo])) {
            return $mapa[$periodo];
        }
        
        // Para períodos personalizados (ex: 61 dias)
        if (str_contains($periodo, 'dias')) {
            return 'personalizado';
        }
        
        return $periodo;
    }
}