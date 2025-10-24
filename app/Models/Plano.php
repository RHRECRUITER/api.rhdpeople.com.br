<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plano extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'preco_mensal',
        'max_usuarios',
        'descricao',
        'ativo',
    ];

    protected $casts = [
        'preco_mensal' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    // Relationships
    public function empresas()
    {
        return $this->hasMany(Empresa::class);
    }
}
