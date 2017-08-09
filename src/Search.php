<?php

namespace Chiragardeshna\Dbextender;

use Illuminate\Http\Request;

class Search
{
    protected $request;

    protected $query;

    public function __construct(Request $request, &$query)
    {
        $this->request = $request;

        $this->query = $query;
    }

    public function build()
    {
        $columns = $this->request->get('columns');
        if ($columns) {
            $this->select($columns);
        }

        $filters = $this->request->except($this->keywords());
        if ($filters) {
            $this->where($filters);
        }

        $fields = $this->request->get('order_by');
        if ($fields) {
            $this->sort($fields);
        }

        return $this->query;
    }

    public function select($columns)
    {
        $columns = explode(',', $columns);
        if (!$columns) {
            return;
        }

        $this->query->select($columns);
    }

    public function where($filters)
    {
        if (!is_array($filters) || !$filters) {
            return;
        }

        array_map(function ($value, $column) {
            $this->query->where($column, $this->operator($value), $this->value($value));
        }, $filters, array_keys($filters));
    }

    public function operator($value)
    {
        $operator = '=';

        $expression = $this->matchExpression($value);
        if (!$expression) {
            return $operator;
        }

        $operator = $this->matchOperator($expression);

        return $operator;
    }

    public function matchExpression($value)
    {
        $match = '';
        $pattern = '/\([:a-z]+\)+/';

        preg_match($pattern, $value, $matches);
        if (!is_array($matches) || !$matches) {
            return $match;
        }

        return array_shift($matches);
    }

    public function removeExpressions($value)
    {
        $operators = $this->operators();

        array_map(function ($operator) use (&$value, $operators) {
            $value = str_replace($operator, '', $value);
        }, array_keys($operators));

        return $value;
    }

    public function matchOperator($expression)
    {
        $operator = '';
        $operators = $this->operators();

        array_map(function ($op, $key) use ($expression, &$operator) {
            if (!in_array($key, ['(', ')']) && strpos($expression, $key) !== false) {
                $operator .= $op;
            }
        }, $operators, array_keys($operators));

        return $operator;
    }

    public function value($value)
    {
        $value = $this->removeExpressions($value);

        array_map(function ($position) use (&$value) {
            if (isset($value[$position])) {
                $value[$position] = ($value[$position] === '*') ? '%' : $value[$position];
            }
        }, [0, strlen($value) - 1]);

        return $value;
    }

    public function sort($fields)
    {
        $fields = explode(',', $fields);
        if (!is_array($fields) || !$fields) {
            return;
        }

        for ($i = 0; $i < count($fields); $i = $i + 2) {
            $field = isset($fields[$i]) && $fields[$i] ? $fields[$i] : '';
            $order = isset($fields[$i + 1]) && in_array($fields[$i + 1], ['asc', 'desc']) ? $fields[$i + 1] : 'asc';
            if (!$field || !$order) {
                continue;
            }
            $this->query->orderby($field, $order);
        }
    }

    public function keywords()
    {
        return [
            'page', 'order_by', 'limit', 'columns', 'except'
        ];
    }

    /**
     * '/\((((:lt)(:eq)?)|((:gt)(:eq)?)|(:not))\)/'
     * */
    public function operators()
    {
        return [
            ':like' => 'LIKE', ':not' => '!=', ':lt' => '<', ':gt' => '>', ':eq' => '=', '(' => '(', ')' => ')'
        ];
    }
}