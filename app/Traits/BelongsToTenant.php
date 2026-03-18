<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    /**
     * Aplica um escopo global por tenant (quando autenticado e não super_admin).
     *
     * Requer colunas:
     * - tenant_id no model/tabela
     * - type e tenant_id no usuário autenticado
     */
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            /** @var User|null $user */
            $user = Auth::user();

            if ($user !== null && $user->type !== 'super_admin') {
                $builder->where('tenant_id', $user->tenant_id);
            }
        });

        static::creating(function (Model $model): void {
            /** @var User|null $user */
            $user = Auth::user();

            if ($user === null || $user->type === 'super_admin') {
                return;
            }

            if (! $model->getAttribute('tenant_id')) {
                $model->setAttribute('tenant_id', $user->tenant_id);
            }
        });
    }
}
