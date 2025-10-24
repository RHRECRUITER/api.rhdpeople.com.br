<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Ponto extends Model
{
    use BelongsToTenant;

    protected $table = 'ponto';

    protected $fillable = [
        'empresa_id', 'colaborador_id', 'data',
        'entrada', 'saida_almoco', 'retorno_almoco', 'saida', 'observacoes'
    ];

    protected $casts = ['data' => 'date'];

    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class);
    }
}
