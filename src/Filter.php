<?php

namespace Aqjw\Filterable;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public abstract function handle(Builder $builder, Request $request);

    /**
     * Determine if the filter should run for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function isActive(Request $request)
    {
        return $request->filled($this->key());
    }

    /**
     * Get the key for the filter in the query string.
     *
     * @return string
     */
    public function key(): string
    {
        return '';
    }

    /**
     * Get the value for the filter from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function value(Request $request)
    {
        return $request->input($this->key());
    }
}