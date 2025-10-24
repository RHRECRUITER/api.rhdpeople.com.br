<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Colaborador extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'colaboradores';

    protected $fillable = [
        'empresa_id',
        'nome',
        'cpf',
        'rg',
        'email',
        'telefone',
        'data_nascimento',
        'genero',
        'cargo',
        'departamento',
        'salario',
        'data_admissao',
        'data_demissao',
        'status',
        'foto_url',
        'observacoes',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'data_admissao' => 'date',
        'data_demissao' => 'date',
        'salario' => 'decimal:2',
    ];

    // Relationships
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function pontos()
    {
        return $this->hasMany(Ponto::class);
    }

    public function ferias()
    {
        return $this->hasMany(Ferias::class);
    }

    public function folhasPagamento()
    {
        return $this->hasMany(FolhaPagamento::class);
    }

    public function beneficios()
    {
        return $this->belongsToMany(Beneficio::class, 'colaborador_beneficios')
            ->withPivot('data_inicio', 'data_fim', 'ativo')
            ->withTimestamps();
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }

    // Helpers
    public function isAtivo()
    {
        return $this->status === 'ativo';
    }
}
