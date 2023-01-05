<?php

namespace Aqjw\Filterable;

use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    /**
     * Filter scope.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param array $filters
     * 
     * @return void
     */
    public function scopeFilters(Builder $builder, array $filters = [])
    {
        Resolver::resolve($builder, $filters, request());
    }
}