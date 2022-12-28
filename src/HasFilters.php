<?php

namespace Aqjw\Filterable;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    /**
     * Filter scope.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param array $filters
     * @return void
     */
    public function scopeFilters(Builder $builder, array $filters = [])
    {
        $request = request();

        foreach ($filters as $filter) {
            if (is_callable($filter)) {
                $builder->where($filter);
            } else if (is_string($filter)) {
                $instance = new $filter;
                if ($instance->isActive($request)) {
                    $instance->handle($builder, $request);
                }
            }
        }
    }
}