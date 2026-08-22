<?php
namespace App\Models\Guest;

use App\Models\Plan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Enums\TypeLicense;
class Invite extends Model{
    use HasUuids;

    protected $table = 'invitations';

    protected $fillable = [
        'name',
        'token',
        'email',
        'type_license',
        'expiration_date',
        'plan_id'
    ];

    protected $casts = [
        'type_license' => TypeLicense::class,
        'expiration_date' => 'datetime',
    ];

    //Relacionamentos
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function use(){
        //Salva a data atual no banco que foi consumido o convite.
        return $this->update(['consumed_at', now()]);
    }

    //Helpers

    public function isExpiredDate(): bool{
        // Verifica se o convite já expirou sua data
        return $this->expirationDate->isBefore(now());
    }
    public function isConsumed(): bool{
        //Verifica se o convite já foi consumido
        return $this->consumed_at !== null;
    }

    public function isValid(): bool{
        //Verifica se o convite está valido
        return !$this->isExpiredDate() && !$this->isConsumed();
    }


}
?>
