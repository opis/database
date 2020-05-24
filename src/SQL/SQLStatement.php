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

class SQLStatement
{
    protected $wheres = [];
    protected $having = [];
    protected $joins = [];
    protected $tables = [];
    protected $columns = [];
    protected $order = [];
    protected $distinct = false;
    protected $group = [];
    protected $limit = 0;
    protected $offset = -1;
    protected $intoTable;
    protected $intoDatabase;
    protected $from = [];
    protected $values = [];

    /**
     * @param Closure $callback
     * @param $separator
     */
    public function addWhereConditionGroup(Closure $callback, $separator)
    {
        $where = new WhereStatement();
        $callback($where);
        $this->wheres[] = [
            'type' => 'whereNested',
            'clause' => $where->getSQLStatement()->getWheres(),
            'separator' => $separator,
        ];
    }

    /**
     * @param string|Closure|Expression $column
     * @param $value
     * @param string $operator
     * @param string $separator
     */
    public function addWhereCondition($column, $value, string $operator, string $separator)
    {
        $this->wheres[] = [
            'type' => 'whereColumn',
            'column' => $this->closureToExpression($column),
            'value' => $this->closureToExpression($value),
            'operator' => $operator,
            'separator' => $separator,
        ];
    }

    /**
     * @param string|Closure|Expression $column
     * @param string $pattern
     * @param string $separator
     * @param bool $not
     */
    public function addWhereLikeCondition($column, string $pattern, string $separator, bool $not)
    {
        $this->wheres[] = [
            'type' => 'whereLike',
            'column' => $this->closureToExpression($column),
            'pattern' => $pattern,
            'separator' => $separator,
            'not' => $not,
        ];
    }

    /**
     * @param string|Closure|Expression $column
     * @param $value1
     * @param $value2
     * @param string $separator
     * @param bool $not
     */
    public function addWhereBetweenCondition($column, $value1, $value2, string $separator, bool $not)
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

    /**
     * @param string|Closure|Expression $column
     * @param $value
     * @param string $separator
     * @param bool $not
     */
    public function addWhereInCondition($column, $value, string $separator, bool $not)
    {
        $column = $this->closureToExpression($column);

        if ($value instanceof Closure) {
            $select = new Subquery();
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

    /**
     * @param string|Closure|Expression $column
     * @param string $separator
     * @param bool $not
     */
    public function addWhereNullCondition($column, string $separator, bool $not)
    {
        $this->wheres[] = [
            'type' => 'whereNull',
            'column' => $this->closureToExpression($column),
            'separator' => $separator,
            'not' => $not,
        ];
    }

    /**
     * @param string|Closure|Expression $column
     * @param string $separator
     */
    public function addWhereNop($column, string $separator) {
        $this->wheres[] = [
            'type' => 'whereNop',
            'column' => $column,
            'separator' => $separator,
        ];
    }

    /**
     * @param Closure $closure
     * @param string $separator
     * @param bool $not
     */
    public function addWhereExistsCondition(Closure $closure, string $separator, bool $not)
    {
        $select = new Subquery();
        $closure($select);

        $this->wheres[] = [
            'type' => 'whereExists',
            'subquery' => $select,
            'separator' => $separator,
            'not' => $not,
        ];
    }

    /**
     * @param  string $type
     * @param  string|array $table
     * @param  Closure $closure
     */
    public function addJoinClause(string $type, $table, Closure $closure = null)
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

    /**
     * @param   Closure $callback
     * @param   string $separator
     */
    public function addHavingGroupCondition(Closure $callback, string $separator)
    {
        $having = new HavingStatement();
        $callback($having);

        $this->having[] = [
            'type' => 'havingNested',
            'conditions' => $having->getSQLStatement()->getHaving(),
            'separator' => $separator,
        ];
    }

    /**
     * @param   string|Closure|Expression $aggregate
     * @param   mixed $value
     * @param   string $operator
     * @param   string $separator
     */
    public function addHavingCondition($aggregate, $value, string $operator, string $separator)
    {
        $this->having[] = [
            'type' => 'havingCondition',
            'aggregate' => $this->closureToExpression($aggregate),
            'value' => $this->closureToExpression($value),
            'operator' => $operator,
            'separator' => $separator,
        ];
    }

    /**
     * @param   string|Closure|Expression $aggregate
     * @param   mixed $value
     * @param   string $separator
     * @param   bool $not
     */
    public function addHavingInCondition($aggregate, $value, string $separator, bool $not)
    {
        $aggregate = $this->closureToExpression($aggregate);

        if ($value instanceof Closure) {
            $select = new Subquery();
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

    /**
     * @param   string|Closure|Expression $aggregate
     * @param   int $value1
     * @param   int $value2
     * @param   string $separator
     * @param   bool $not
     */
    public function addHavingBetweenCondition($aggregate, $value1, $value2, string $separator, bool $not)
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

    /**
     * @param array $tables
     */
    public function addTables(array $tables)
    {
        $this->tables = $tables;
    }

    /**
     * @param array $columns
     */
    public function addUpdateColumns(array $columns)
    {
        foreach ($columns as $column => $value) {
            $this->columns[] = [
                'column' => $column,
                'value' => $this->closureToExpression($value),
            ];
        }
    }

    /**
     * @param string[]|Expression[]|Closure[] $columns
     * @param string $order
     * @param string|null $nulls
     */
    public function addOrder(array $columns, string $order, string $nulls = null)
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

    /**
     * @param string[]|Expression[]|Closure[] $columns
     */
    public function addGroupBy(array $columns)
    {
        foreach ($columns as &$column) {
            $column = $this->closureToExpression($column);
        }

        $this->group = $columns;
    }

    /**
     * @param string|Closure|Expression $column
     * @param null $alias
     */
    public function addColumn($column, $alias = null)
    {
        $this->columns[] = [
            'name' => $this->closureToExpression($column),
            'alias' => $alias,
        ];
    }

    /**
     * @param bool $value
     */
    public function setDistinct(bool $value)
    {
        $this->distinct = $value;
    }

    /**
     * @param int $value
     */
    public function setLimit(int $value)
    {
        $this->limit = $value;
    }

    /**
     * @param int $value
     */
    public function setOffset(int $value)
    {
        $this->offset = $value;
    }

    /**
     * @param string $table
     * @param string|null $database
     */
    public function setInto(string $table, string $database = null)
    {
        $this->intoTable = $table;
        $this->intoDatabase = $database;
    }

    /**
     * @param array $from
     */
    public function setFrom(array $from)
    {
        $this->from = $from;
    }

    /**
     * @param $value
     */
    public function addValue($value)
    {
        $this->values[] = $this->closureToExpression($value);
    }

    /**
     * @return array
     */
    public function getWheres(): array
    {
        return $this->wheres;
    }

    /**
     * @return array
     */
    public function getHaving(): array
    {
        return $this->having;
    }

    /**
     * @return array
     */
    public function getJoins(): array
    {
        return $this->joins;
    }

    /**
     * @return bool
     */
    public function getDistinct(): bool
    {
        return $this->distinct;
    }

    /**
     * @return array
     */
    public function getTables(): array
    {
        return $this->tables;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getOrder(): array
    {
        return $this->order;
    }

    /**
     * @return array
     */
    public function getGroupBy(): array
    {
        return $this->group;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return string|null
     */
    public function getIntoTable()
    {
        return $this->intoTable;
    }

    /**
     * @return string|null
     */
    public function getIntoDatabase()
    {
        return $this->intoDatabase;
    }

    /**
     * @return array
     */
    public function getFrom(): array
    {
        return $this->from;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param $value
     * @return mixed|Expression
     */
    protected function closureToExpression($value)
    {
        if ($value instanceof Closure) {
            return Expression::fromClosure($value);
        }

        return $value;
    }
}