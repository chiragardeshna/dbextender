<?php

namespace Chiragardeshna\Dbextender\Eloquent;

abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \App\Builder|\Illuminate\Database\Query\Builder
     * @throws \Exception
     */
    protected function newBaseQueryBuilder()
    {
        $conn = $this->getConnection();

        $grammar = $conn->getQueryGrammar();

        $builder = config('dbextender.builder');

        $builder = $builder ?: \Illuminate\Database\Query\Builder::class;

        return new $builder($conn, $grammar, $conn->getPostProcessor());
    }
}
