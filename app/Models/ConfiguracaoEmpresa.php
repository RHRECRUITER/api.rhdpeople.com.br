<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracaoEmpresa extends Model
{
    protected $table = 'configuracoes_empresa';

    protected $fillable = [
        'empresa_id',
        'logo_url',
        'cor_primaria',
        'cor_secundaria',
        'layout',
        'modulos_ativos',
    ];

    protected $casts = [
        'modulos_ativos' => 'array',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
