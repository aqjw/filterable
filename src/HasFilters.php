<?php

namespace Aqjw\Filterable;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    public function scopeFilters(Builder $builder, array $filters = [])
    {
        foreach ($filters as $filter) {
            $builder->where($filter);
        }
    }
}