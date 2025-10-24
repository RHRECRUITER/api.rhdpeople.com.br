<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToTenant
{
    /**
     * Boot do trait - aplica scope global
     */
    protected static function bootBelongsToTenant()
    {
        // Se não for super admin, filtrar por empresa_id
        static::addGlobalScope('empresa', function (Builder $builder) {
            if (auth()->check() && auth()->user()->tipo !== 'super_admin') {
                $builder->where('empresa_id', auth()->user()->empresa_id);
            }
        });

        // Ao criar, adiciona empresa_id automaticamente
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->tipo !== 'super_admin') {
                if (!$model->empresa_id) {
                    $model->empresa_id = auth()->user()->empresa_id;
                }
            }
        });
    }
}
