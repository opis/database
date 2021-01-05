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

class Join
{
    protected array $conditions = [];

    public function getJoinConditions(): array
    {
        return $this->conditions;
    }

    public function on(mixed $column1, mixed $column2 = null, string $operator = '='): static
    {
        return $this->addJoinCondition($column1, $column2, $operator);
    }

    public function andOn(mixed $column1, mixed $column2 = null, string $operator = '='): static
    {
        return $this->addJoinCondition($column1, $column2, $operator);
    }

    public function orOn(mixed $column1, mixed $column2 = null, string $operator = '='): static
    {
        return $this->addJoinCondition($column1, $column2, $operator, 'OR');
    }

    protected function addJoinExpression(mixed $expression, string $separator = 'AND'): static
    {
        if ($expression instanceof Closure) {
            $expression = Expression::fromClosure($expression);
        }

        $this->conditions[] = [
            'type' => 'joinExpression',
            'expression' => $expression,
            'separator' => $separator,
        ];

        return $this;
    }

    protected function addJoinCondition(mixed $column1, mixed $column2, string $operator, string $separator = 'AND'): static
    {
        if ($column1 instanceof Closure) {
            if ($column2 === true) {
                return $this->addJoinExpression($column1, $separator);
            }

            if ($column2 === null) {
                $join = new Join();
                $column1($join);

                $this->conditions[] = [
                    'type' => 'joinNested',
                    'join' => $join,
                    'separator' => $separator,
                ];

                return $this;
            }

            $column1 = Expression::fromClosure($column1);
        } elseif (($column1 instanceof Expression) && $column2 === true) {
            return $this->addJoinExpression($column1, $separator);
        }

        if ($column2 instanceof Closure) {
            $column2 = Expression::fromClosure($column2);
        }

        $this->conditions[] = [
            'type' => 'joinColumn',
            'column1' => $column1,
            'column2' => $column2,
            'operator' => $operator,
            'separator' => $separator,
        ];

        return $this;
    }
}
