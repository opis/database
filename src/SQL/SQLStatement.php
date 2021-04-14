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

class SQLStatement
{
    protected array $wheres = [];
    protected array $having = [];
    protected array $joins = [];
    protected array $tables = [];
    protected array $columns = [];
    protected array $order = [];
    protected bool $distinct = false;
    protected array $group = [];
    protected ?int $limit = null;
    protected ?int $offset = null;
    protected ?string $intoTable = null;
    protected ?string $intoDatabase = null;
    protected array $from = [];
    protected array $values = [];

    /**
     * @param Closure $callback
     * @param string $separator
     */
    public function addWhereConditionGroup(Closure $callback, string $separator): void
    {
        $where = new WhereStatement();
        $callback($where);
        $this->wheres[] = [
            'type' => 'whereNested',
            'clause' => $where->getSQLStatement()->getWheres(),
            'separator' => $separator,
        ];
    }

    public function addWhereCondition(mixed $column, $value, string $operator, string $separator): void
    {
        $this->wheres[] = [
            'type' => 'whereColumn',
            'column' => $this->closureToExpression($column),
            'value' => $this->closureToExpression($value),
            'operator' => $operator,
            'separator' => $separator,
        ];
    }

    public function addWhereLikeCondition(mixed $column, string $pattern, string $separator, bool $not): void
    {
        $this->wheres[] = [
            'type' => 'whereLike',
            'column' => $this->closureToExpression($column),
            'pattern' => $pattern,
            'separator' => $separator,
            'not' => $not,
        ];
    }

    public function addWhereBetweenCondition(mixed $column, mixed $value1, mixed $value2, string $separator, bool $not): void
    {
        $this->wheres[] = [
            'type' => 'whereBetween',
            'column' => $this->closureToExpression($column),
            'value1' => $this->closureToExpression($value1),
            'value2' => $this->closureToExpression($value2),
            'separator' => $separator,
            'not' => $not,
        ];
    }

    public function addWhereInCondition(mixed $column, mixed $value, string $separator, bool $not): void
    {
        $column = $this->closureToExpression($column);

        if ($value instanceof Closure) {
            $select = new SubQuery();
            $value($select);
            $this->wheres[] = [
                'type' => 'whereInSelect',
                'column' => $column,
                'subquery' => $select,
                'separator' => $separator,
                'not' => $not,
            ];
        } else {
            $this->wheres[] = [
                'type' => 'whereIn',
                'column' => $column,
                'value' => $value,
                'separator' => $separator,
                'not' => $not,
            ];
        }
    }

    public function addWhereNullCondition(mixed $column, string $separator, bool $not): void
    {
        $this->wheres[] = [
            'type' => 'whereNull',
            'column' => $this->closureToExpression($column),
            'separator' => $separator,
            'not' => $not,
        ];
    }

    public function addWhereNop(mixed $column, string $separator): void
    {
        $this->wheres[] = [
            'type' => 'whereNop',
            'column' => $column,
            'separator' => $separator,
        ];
    }

    public function addWhereExistsCondition(Closure $closure, string $separator, bool $not): void
    {
        $select = new SubQuery();
        $closure($select);

        $this->wheres[] = [
            'type' => 'whereExists',
            'subquery' => $select,
            'separator' => $separator,
            'not' => $not,
        ];
    }

    public function addJoinClause(string $type, mixed $table, ?Closure $closure = null): void
    {
        $join = null;
        if ($closure) {
            $join = new Join();
            $closure($join);
        }

        if ($table instanceof Closure) {
            $table = Expression::fromClosure($table);
        }

        if (!is_array($table)) {
            $table = [$table];
        }

        $this->joins[] = [
            'type' => $type,
            'table' => $table,
            'join' => $join,
        ];
    }

    public function addHavingGroupCondition(Closure $callback, string $separator): void
    {
        $having = new HavingStatement();
        $callback($having);

        $this->having[] = [
            'type' => 'havingNested',
            'conditions' => $having->getSQLStatement()->getHaving(),
            'separator' => $separator,
        ];
    }

    public function addHavingCondition(mixed $aggregate, mixed $value, string $operator, string $separator): void
    {
        $this->having[] = [
            'type' => 'havingCondition',
            'aggregate' => $this->closureToExpression($aggregate),
            'value' => $this->closureToExpression($value),
            'operator' => $operator,
            'separator' => $separator,
        ];
    }

    public function addHavingInCondition(mixed $aggregate, mixed $value, string $separator, bool $not): void
    {
        $aggregate = $this->closureToExpression($aggregate);

        if ($value instanceof Closure) {
            $select = new SubQuery();
            $value($select);
            $this->having[] = [
                'type' => 'havingInSelect',
                'aggregate' => $aggregate,
                'subquery' => $select,
                'separator' => $separator,
                'not' => $not,
            ];
        } else {
            $this->having[] = [
                'type' => 'havingIn',
                'aggregate' => $aggregate,
                'value' => $value,
                'separator' => $separator,
                'not' => $not,
            ];
        }
    }

    public function addHavingBetweenCondition(mixed $aggregate, mixed $value1, mixed $value2, string $separator, bool $not): void
    {
        $this->having[] = [
            'type' => 'havingBetween',
            'aggregate' => $this->closureToExpression($aggregate),
            'value1' => $this->closureToExpression($value1),
            'value2' => $this->closureToExpression($value2),
            'separator' => $separator,
            'not' => $not,
        ];
    }

    public function addTables(array $tables): void
    {
        $this->tables = $tables;
    }

    public function addUpdateColumns(array $columns): void
    {
        foreach ($columns as $column => $value) {
            $this->columns[] = [
                'column' => $column,
                'value' => $this->closureToExpression($value),
            ];
        }
    }

    public function addOrder(array $columns, string $order, ?string $nulls = null): void
    {
        foreach ($columns as &$column) {
            $column = $this->closureToExpression($column);
        }

        $order = strtoupper($order);

        if ($order !== 'ASC' && $order !== 'DESC') {
            $order = 'ASC';
        }

        if ($nulls !== null) {
            $nulls = strtoupper($nulls);

            if ($nulls !== 'NULLS FIRST' && $nulls !== 'NULLS LAST') {
                $nulls = null;
            }
        }

        $this->order[] = [
            'columns' => $columns,
            'order' => $order,
            'nulls' => $nulls,
        ];
    }

    public function addGroupBy(array $columns): void
    {
        foreach ($columns as &$column) {
            $column = $this->closureToExpression($column);
        }

        $this->group = $columns;
    }

    public function addColumn(mixed $column, ?string $alias = null): void
    {
        $this->columns[] = [
            'name' => $this->closureToExpression($column),
            'alias' => $alias,
        ];
    }

    public function setDistinct(bool $value): void
    {
        $this->distinct = $value;
    }

    public function setLimit(int $value): void
    {
        $this->limit = $value;
    }

    public function setOffset(int $value): void
    {
        $this->offset = $value;
    }

    public function setInto(string $table, ?string $database = null): void
    {
        $this->intoTable = $table;
        $this->intoDatabase = $database;
    }

    public function setFrom(array $from): void
    {
        $this->from = $from;
    }

    public function addValues(array $values): void
    {
        $this->values[] = array_map([$this, 'closureToExpression'], $values);
    }

    public function getWheres(): array
    {
        return $this->wheres;
    }

    public function getHaving(): array
    {
        return $this->having;
    }

    public function getJoins(): array
    {
        return $this->joins;
    }

    public function getDistinct(): bool
    {
        return $this->distinct;
    }

    public function getTables(): array
    {
        return $this->tables;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getOrder(): array
    {
        return $this->order;
    }

    public function getGroupBy(): array
    {
        return $this->group;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getIntoTable(): ?string
    {
        return $this->intoTable;
    }

    public function getIntoDatabase(): ?string
    {
        return $this->intoDatabase;
    }

    public function getFrom(): array
    {
        return $this->from;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    protected function closureToExpression(mixed $value): mixed
    {
        if ($value instanceof Closure) {
            return Expression::fromClosure($value);
        }

        return $value;
    }
}