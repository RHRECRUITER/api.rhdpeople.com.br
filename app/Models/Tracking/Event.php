<?php

namespace App\Models\Tracking;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasUuids;

    protected $connection = 'conn_rhdpeople_landingpage';

    protected $table = 'tracking_events';

    public $timestamps = false;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'session_id',
        'tenant_id',
        'user_id',
        'event_type',
        'url',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relacionamento com o Bloco de Eventos (agregado)
     * Usamos o session_id como chave de ligação lógica
     */
    public function block()
    {
        // Se você renomeou o model de Session para EventBlock:
        return $this->belongsTo(EventBlock::class, 'session_id', 'session_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'tenant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
