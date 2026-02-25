<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ZoneScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();
        if ($user && !$user->hasRole('admin') && $user->zone_id) {
            $builder->where($model->getTable() . '.zone_id', $user->zone_id);
        }
    }
}
