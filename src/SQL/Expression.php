<?php
/* ===========================================================================
 * Copyright 2018-2021 Zindex Software
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
    protected array $expressions = [];

    public function getExpressions(): array
    {
        return $this->expressions;
    }

    public function column(mixed $value): static
    {
        return $this->addExpression('column', $value);
    }

    public function op(mixed $value): static
    {
        return $this->addExpression('op', $value);
    }

    public function value(mixed $value): static
    {
        return $this->addExpression('value', $value);
    }

    public function group(Closure $closure): static
    {
        $expression = new Expression();
        $closure($expression);
        return $this->addExpression('group', $expression);
    }

    public function from(string|array $tables): SelectStatement
    {
        $subQuery = new SubQuery();
        $this->addExpression('subquery', $subQuery);
        return $subQuery->from($tables);
    }

    public function count(mixed $column = '*', bool $distinct = false): static
    {
        if (!is_array($column)) {
            $column = [$column];
        }
        $distinct = $distinct || (count($column) > 1);
        return $this->addFunction('aggregateFunction', 'COUNT', $column, ['distinct' => $distinct]);
    }

    public function sum(mixed $column, bool $distinct = false): static
    {
        return $this->addFunction('aggregateFunction', 'SUM', $column, ['distinct' => $distinct]);
    }

    public function avg(mixed $column, bool $distinct = false): static
    {
        return $this->addFunction('aggregateFunction', 'AVG', $column, ['distinct' => $distinct]);
    }

    public function max(mixed $column, bool $distinct = false): static
    {
        return $this->addFunction('aggregateFunction', 'MAX', $column, ['distinct' => $distinct]);
    }

    public function min(mixed $column, bool $distinct = false): static
    {
        return $this->addFunction('aggregateFunction', 'MIN', $column, ['distinct' => $distinct]);
    }

    public function ucase(mixed $column): static
    {
        return $this->addFunction('sqlFunction', 'UCASE', $column);
    }

    public function lcase(mixed $column): static
    {
        return $this->addFunction('sqlFunction', 'LCASE', $column);
    }

    public function mid(mixed $column, int $start = 1, int $length = 0): static
    {
        return $this->addFunction('sqlFunction', 'MID', $column, ['start' => $start, 'length' => $length]);
    }

    public function len(mixed $column): static
    {
        return $this->addFunction('sqlFunction', 'LEN', $column);
    }

    public function round(mixed $column, int $decimals = 0): static
    {
        return $this->addFunction('sqlFunction', 'ROUND', $column, ['decimals' => $decimals]);
    }

    public function now(): static
    {
        return $this->addFunction('sqlFunction', 'NOW', '');
    }

    public function format(mixed $column, mixed $format): static
    {
        return $this->addFunction('sqlFunction', 'FORMAT', $column, ['format' => $format]);
    }

    public function call(string $func, array $args = []): static
    {
        if ($args) {
            foreach ($args as $key => $arg) {
                if ($arg instanceof Closure) {
                    $args[$key] = self::fromClosure($arg);
                }
            }
        }

        return $this->addExpression('call', ['name' => $func, 'args' => $args]);
    }

    public function __get(mixed $value): static
    {
        return $this->addExpression('op', $value);
    }

    public function __call(string $name, array $arguments): static
    {
        return $this->call($name, $arguments);
    }

    public static function fromClosure(Closure $func): static
    {
        $expression = new Expression();
        $func($expression);
        return $expression;
    }

    public static function fromColumn(string $column): static
    {
        $expression = new static();
        $expression->column($column);
        return $expression;
    }

    public static function fromCall(string $func, mixed ...$args): static
    {
        $expression = new static();
        $expression->call($func, $args);
        return $expression;
    }

    protected function addExpression(string $type, mixed $value): static
    {
        $this->expressions[] = [
            'type' => $type,
            'value' => $value,
        ];

        return $this;
    }

    protected function addFunction(string $type, string $name, mixed $column, array $arguments = []): static
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

        $func = ['type' => $type, 'name' => $name, 'column' => $column];

        if ($arguments) {
            $func = array_merge($func, $arguments);
        }

        return $this->addExpression('function', $func);
    }
}
