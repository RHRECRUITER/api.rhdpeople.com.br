<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'empresa_id', 'colaborador_id', 'titulo', 'descricao',
        'arquivo_url', 'tipo_arquivo', 'tamanho', 'categoria'
    ];

    public function colaborador()
    {
        return $this->belongsTo(Colaborador::class);
    }
}
