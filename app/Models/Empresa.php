<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'plano_id',
        'nome',
        'cnpj',
        'email',
        'telefone',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'status',
        'data_inicio',
        'data_fim',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
    ];

    // Relationships
    public function plano()
    {
        return $this->belongsTo(Plano::class);
    }

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function configuracao()
    {
        return $this->hasOne(ConfiguracaoEmpresa::class);
    }

    public function colaboradores()
    {
        return $this->hasMany(Colaborador::class);
    }

    public function logsAcesso()
    {
        return $this->hasMany(LogAcesso::class);
    }

    // Helpers
    public function isAtiva()
    {
        return $this->status === 'ativa';
    }
}
