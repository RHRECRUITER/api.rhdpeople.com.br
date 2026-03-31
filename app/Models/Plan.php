<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


use App\Enums\SubscriptionInterval;
use App\Models\User;
use App\Models\Contract;

class Plan extends Model
{
    //Uuids
    use HasUuids, HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';

    //Colunas de injeção de depedencia
    protected $fillable = [
        'type_plan',
        'number_employees',
        'subscription_interval',
        'modules',
        'add_ons',
        'contract_id',
        'company_id',
    ];

    //conversões implicitas
    protected $casts = [
        'subscription_interval' => SubscriptionInterval::class,
        'price' => 'decimal:2',
        'price_subscription' => 'decimal:2',
        'modules' => 'array',
        'add_ons' => 'array',
        'expiration_date_contract' => 'date:d-m',
    ];
    //

    //Relacionamento com o contrato
    public function contract(): belongsTo{
        return $this->belongsTo(Contract::class);
    }
    //Relacionamento com Licensa
    public function licenses(): hasMany{
        return $this->hasMany(License::class);
    }


}
