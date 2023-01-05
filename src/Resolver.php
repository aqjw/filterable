<?php

namespace Aqjw\Filterable;

use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;

class Resolver
{
    public static function resolve(Builder $builder, array $filters, Request $request)
    {
        $nextOperator = 'and';

        foreach ($filters as $filter) {
            if (is_callable($filter)) {
                $builder->where($filter, boolean: $nextOperator);
            } else if (is_array($filter)) {
                $builder->where(
                    fn(Builder $query) => self::resolve($query, $filter, $request),
                    boolean: $nextOperator,
                );
            } else if (is_string($filter) && $filter === 'or') {
                $nextOperator = 'or';
                continue;
            } else if (is_string($filter)) {
                $builder->where(
                    fn(Builder $query) => self::applyFilter($query, $filter, $request),
                    boolean: $nextOperator,
                );
            }

            $nextOperator = 'and';
        }
    }

    public static function applyFilter(Builder $builder, string $filter, Request $request)
    {
        $instance = new $filter;
        if ($instance->isActive($request)) {
            $instance->handle($builder, $request);
        }
    }
}