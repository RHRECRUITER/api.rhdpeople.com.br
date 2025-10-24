<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAcesso extends Model
{
    protected $table = 'logs_acesso';

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'empresa_id', 'ip', 'user_agent', 'acao', 'data_hora'
    ];

    protected $casts = ['data_hora' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
