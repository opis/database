<?php
/* ===========================================================================
 * Copyright 2018 Zindex Software
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\Database\SQL;

use Closure;

class Expression
{
    /** @var    array */
    protected $expressions = [];

    /**
     * Returns an array of expressions
     *
     * @return  array
     */
    public function getExpressions()
    {
        return $this->expressions;
    }

    /**
     * @param   string $type
     * @param   mixed $value
     *
     * @return  $this
     */
    protected function addExpression(string $type, $value)
    {
        $this->expressions[] = [
            'type' => $type,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * @param   string $type
     * @param   string $name
     * @param   Closure|string $column
     * @param   array $arguments (optional)
     *
     * @return  $this
     */
    protected function addFunction(string $type, string $name, $column, array $arguments = []): self
    {
        if ($column instanceof Closure) {
            $column = Expression::fromClosure($column);
        } elseif (is_array($column)) {
            foreach ($column as &$c) {
                if ($c instanceof Closure) {
                    $c = Expression::fromClosure($c);
                }
            }
        }

        $func = array_merge(['type' => $type, 'name' => $name, 'column' => $column], $arguments);

        return $this->addExpression('function', $func);
    }

    /**
     * @param   mixed $value
     *
     * @return  $this
     */
    public function column($value): self
    {
        return $this->addExpression('column', $value);
    }

    /**
     * @param   mixed $value
     *
     * @return  $this
     */
    public function op($value): self
    {
        return $this->addExpression('op', $value);
    }

    /**
     * @param   mixed $value
     * @return  $this
     */
    public function value($value): self
    {
        return $this->addExpression('value', $value);
    }

    /**
     * @param   Closure $closure
     *
     * @return  $this
     */
    public function group(Closure $closure): self
    {
        $expression = new Expression();
        $closure($expression);
        return $this->addExpression('group', $expression);
    }

    /**
     * @param   array|string $tables
     *
     * @return  SelectStatement
     */
    public function from($tables): SelectStatement
    {
        $subquery = new Subquery();
        $this->addExpression('subquery', $subquery);
        return $subquery->from($tables);
    }

    /**
     * @param   string|array $column (optional)
     * @param   bool $distinct (optional)
     *
     * @return  $this
     */
    public function count($column = '*', bool $distinct = false): self
    {
        if (!is_array($column)) {
            $column = [$column];
        }
        $distinct = $distinct || (count($column) > 1);
        return $this->addFunction('aggregateFunction', 'COUNT', $column, ['distinct' => $distinct]);
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     *
     * @return  $this
     */
    public function sum($column, bool $distinct = false): self
    {
        return $this->addFunction('aggregateFunction', 'SUM', $column, ['distinct' => $distinct]);
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     *
     * @return  $this
     */
    public function avg($column, bool $distinct = false): self
    {
        return $this->addFunction('aggregateFunction', 'AVG', $column, ['distinct' => $distinct]);
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     *
     * @return  $this
     */
    public function max($column, bool $distinct = false): self
    {
        return $this->addFunction('aggregateFunction', 'MAX', $column, ['distinct' => $distinct]);
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     *
     * @return  $this
     */
    public function min($column, bool $distinct = false): self
    {
        return $this->addFunction('aggregateFunction', 'MIN', $column, ['distinct' => $distinct]);
    }

    /**
     * @param   string $column
     *
     * @return  $this
     */
    public function ucase($column): self
    {
        return $this->addFunction('sqlFunction', 'UCASE', $column);
    }

    /**
     * @param   string $column
     *
     * @return  $this
     */
    public function lcase($column): self
    {
        return $this->addFunction('sqlFunction', 'LCASE', $column);
    }

    /**
     * @param   string $column
     * @param   int $start (optional)
     * @param   int $length (optional)
     *
     * @return  $this
     */
    public function mid($column, int $start = 1, int $length = 0): self
    {
        return $this->addFunction('sqlFunction', 'MID', $column, ['start' => $start, 'length' => $length]);
    }

    /**
     * @param   string $column
     *
     * @return  $this
     */
    public function len($column): self
    {
        return $this->addFunction('sqlFunction', 'LEN', $column);
    }

    /**
     * @param   string $column
     * @param   int $decimals (optional)
     *
     * @return  $this
     */
    public function round($column, int $decimals = 0): self
    {
        return $this->addFunction('sqlFunction', 'ROUND', $column, ['decimals' => $decimals]);
    }

    /**
     * @return  $this
     */
    public function now(): self
    {
        return $this->addFunction('sqlFunction', 'NOW', '');
    }

    /**
     * @param $column
     * @param $format
     * @return Expression
     */
    public function format($column, $format): self
    {
        return $this->addFunction('sqlFunction', 'FORMAT', $column, ['format' => $format]);
    }

    /**
     * @param   mixed $value
     *
     * @return  $this
     */
    public function __get($value)
    {
        return $this->addExpression('op', $value);
    }

    /**
     * @param Closure $func
     * @return self
     */
    public static function fromClosure(Closure $func): self
    {
        $expression = new Expression();
        $func($expression);
        return $expression;
    }
}
