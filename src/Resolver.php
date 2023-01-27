<?php
namespace Aqjw\Filterable;

use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Eloquent\Builder;

class Resolver
{
    /**
     * Resolves the given filters and applies them to the given builder instance.
     *
     * @param Builder $builder The builder instance to apply the filters to.
     * @param array $filters An array of filters to apply.
     * @param Request $request The current request instance.
     * 
     * @return void
     */
    public static function resolve(Builder $builder, array $filters, Request $request)
    {
        $nextOperator = 'and';

        foreach ($filters as $filter) {
            if (is_callable($filter)) {
                // If the filter is a callable, apply it directly to the query builder
                $builder->where($filter, boolean: $nextOperator);
            } else if (is_array($filter)) {
                // If the filter is an array, recursively resolve it and apply the result to the query builder
                $builder->where(
                    fn(Builder $query) => self::resolve($query, $filter, $request),
                    boolean: $nextOperator,
                );
            } else if (is_string($filter) && $filter === 'or') {
                // If the filter is a string "or", set the next operator to "or"
                $nextOperator = 'or';
                continue;
            } else if (is_string($filter)) {
                // If the filter is a string that represents a filter class, apply the filter to the query builder
                $builder->where(
                    fn(Builder $query) => self::applyFilter($query, $filter, $request),
                    boolean: $nextOperator,
                );
            }

            $nextOperator = 'and';
        }
    }

    /**
     * Applies a single filter to the given builder instance.
     *
     * @param Builder $builder The builder instance to apply the filter to.
     * @param string $filter The filter class to apply.
     * @param Request $request The current request instance.
     * 
     * @return void
     */
    public static function applyFilter(Builder $builder, string $filter, Request $request)
    {
        $instance = new $filter;
        // Check if the filter is active based on the request parameters
        if ($instance->isActive($request)) {
            // Apply the filter to the query builder
            $instance->apply($builder, $instance->value($request));
        }
    }
}