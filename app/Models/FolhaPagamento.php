<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class FolhaPagamento extends Model
{
    use BelongsToTenant;

    protected $table = 'folha_pagamento';

    protected $fillable = [
        'empresa_id', 'colaborador_id', 'mes', 'ano',
        'salario_base', 'horas_extras', 'descontos',
        'bonificacoes', 'salario_liquido', 'status', 'data_pagamento'
    ];

    protected $casts = [
        'salario_base' => 'decimal:2',
        'horas_extras' => 'decimal:2',
        'descontos' => 'decimal:2',
        'bonificacoes' => 'decimal:2',
        'salario_liquido' => 'decimal:2',
        'data_pagamento' => 'date',
    ];

    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class);
    }
}
