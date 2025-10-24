<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Beneficio extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'empresa_id', 'nome', 'descricao', 'valor', 'tipo', 'ativo'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    public function colaboradores()
    {
        return $this->belongsToMany(Colaborador::class, 'colaborador_beneficios')
            ->withPivot('data_inicio', 'data_fim', 'ativo')
            ->withTimestamps();
    }
}
