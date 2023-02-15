<?php

namespace Aqjw\Filterable;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param mixed $value
     * 
     * @return void
     */
    public abstract function apply(Builder $query, mixed $value);

    /**
     * Determine if the filter should run for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return bool
     */
    public function isActive(Request $request)
    {
        return $request->filled($this->key());
    }

    /**
     * Get the key for the filter.
     *
     * @return string
     */
    public function key(): string
    {
        // use the class name as the default filter key
        $key = Str::snake(class_basename($this));

        // remove 'by_' prefix from the key, if it exists
        $key = Str::replaceFirst('by_', '', $key);

        return $key;
    }

    /**
     * Get the value for the filter from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return mixed
     */
    public function value(Request $request)
    {
        return $request->input($this->key());
    }
}