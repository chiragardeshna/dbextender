<?php

namespace Chiragardeshna\Dbextender\Query;

class MysqlConnection extends \Illuminate\Database\MySqlConnection
{
    /**
     * Get a new query builder instance.
     *
     * @return \App\Builder|\Illuminate\Database\Query\Builder
     * @throws \Exception
     */
    public function query()
    {
        $builder = config('dbextender.builder');

        $builder = $builder ?: \Illuminate\Database\Query\Builder::class;

        return new $builder(
            $this, $this->getQueryGrammar(), $this->getPostProcessor()
        );
    }
}
