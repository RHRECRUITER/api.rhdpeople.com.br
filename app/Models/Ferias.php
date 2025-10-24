<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Ferias extends Model
{
    use BelongsToTenant;

    protected $table = 'ferias';

    protected $fillable = [
        'empresa_id', 'colaborador_id', 'tipo', 'data_inicio',
        'data_fim', 'dias', 'status', 'observacoes'
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class);
    }
}
