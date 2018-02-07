<?php
/* ===========================================================================
 * Copyright 2013-2016 The Opis Project
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
    protected $tables= [];
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
        $this->wheres[] = array(
            'type' => 'whereNested',
            'clause' => $where->getSQLStatement()->getWheres(),
            'separator' => $separator
        );
    }

    /**
     * @param string $column
     * @param $value
     * @param string $operator
     * @param string $separator
     */
    public function addWhereCondition(string $column, $value, string $operator, string $separator)
    {
        if($value instanceof Closure) {
            $expr = new Expression();
            $value($expr);
            $value = $expr;
        }

        $this->wheres[] = array(
            'type' => 'whereColumn',
            'column' => $column,
            'value' => $value,
            'operator' => $operator,
            'separator' => $separator,
        );
    }

    /**
     * @param $column
     * @param $pattern
     * @param $separator
     * @param $not
     */
    public function addWhereLikeCondition(string $column, string $pattern, string $separator, bool $not)
    {
        $this->wheres[] = array(
            'type' => 'whereLike',
            'column' => $column,
            'pattern' => $pattern,
            'separator' => $separator,
            'not' => $not,
        );
    }

    /**
     * @param string $column
     * @param $value1
     * @param $value2
     * @param string $separator
     * @param bool $not
     */
    public function addWhereBetweenCondition(string $column, $value1, $value2, string $separator, bool $not)
    {
        $this->wheres[] = array(
            'type' => 'whereBetween',
            'column' => $column,
            'value1' => $value1,
            'value2' => $value2,
            'separator' => $separator,
            'not' => $not,
        );
    }
    /**
     * @param $column
     * @param $value
     * @param $separator
     * @param $not
     */
    public function addWhereInCondition(string $column, $value, string $separator, bool $not)
    {
        if ($value instanceof Closure) {
            $select = new Subquery();
            $value($select);
            $this->wheres[] = array(
                'type' => 'whereInSelect',
                'column' => $column,
                'subquery' => $select,
                'separator' => $separator,
                'not' => $not,
            );
        } else {
            $this->wheres[] = array(
                'type' => 'whereIn',
                'column' => $column,
                'value' => $value,
                'separator' => $separator,
                'not' => $not,
            );
        }
    }

    /**
     * @param string $column
     * @param string $separator
     * @param bool $not
     */
    public function addWhereNullCondition(string $column, string $separator, bool $not)
    {
        $this->wheres[] = array(
            'type' => 'whereNull',
            'column' => $column,
            'separator' => $separator,
            'not' => $not,
        );
    }

    /**
     * @param $closure
     * @param $separator
     * @param $not
     */
    public function addWhereExistsCondition(Closure $closure, string $separator, bool $not)
    {
        $select = new Subquery();
        $closure($select);

        $this->wheres[] = array(
            'type' => 'whereExists',
            'subquery' => $select,
            'separator' => $separator,
            'not' => $not,
        );
    }

    /**
     *  @param  string          $type
     *  @param  string|array    $table
     *  @param  Closure         $closure
     */
    public function addJoinClause(string $type, $table, Closure $closure)
    {
        $join = new Join();
        $closure($join);

        if (!is_array($table)) {
            $table = array($table);
        }

        $this->joins[] = array(
            'type' => $type,
            'table' => $table,
            'join' => $join,
        );
    }

    /**
     * @param   Closure $callback
     * @param   string  $separator
     */
    public function addHavingGroupCondition(Closure $callback, string $separator)
    {
        $having = new HavingStatement();
        $callback($having);

        $this->having[] = array(
            'type' => 'havingNested',
            'conditions' => $having->getSQLStatement()->getHaving(),
            'separator' => $separator,
        );
    }

    /**
     * @param   string  $aggregate
     * @param   mixed   $value
     * @param   string  $operator
     * @param   string  $separator
     */
    public function addHavingCondition(string $aggregate, $value, string $operator, string $separator)
    {
        if ($value instanceof Closure) {
            $expr = new Expression();
            $value($expr);
            $value = $expr;
        }

        $this->having[] = array(
            'type' => 'havingCondition',
            'aggregate' => $aggregate,
            'value' => $value,
            'operator' => $operator,
            'separator' => $separator,
        );
    }

    /**
     * @param   string  $aggregate
     * @param   mixed   $value
     * @param   string  $separator
     * @param   bool    $not
     */
    public function addHavingInCondition(string $aggregate, $value, string $separator, bool $not)
    {
        if ($value instanceof Closure) {
            $select = new Subquery();
            $value($select);
            $this->having[] = array(
                'type' => 'havingInSelect',
                'aggregate' => $aggregate,
                'subquery' => $select,
                'separator' => $separator,
                'not' => $not,
            );
        } else {
            $this->having[] = array(
                'type' => 'havingIn',
                'aggregate' => $aggregate,
                'value' => $value,
                'separator' => $separator,
                'not' => $not,
            );
        }
    }

    /**
     * @param   string  $aggregate
     * @param   int     $value1
     * @param   int     $value2
     * @param   string  $separator
     * @param   bool    $not
     */
    public function addHavingBetweenCondition(string $aggregate, $value1, $value2, string $separator, bool $not)
    {
        $this->having[] = array(
            'type' => 'havingBetween',
            'aggregate' => $aggregate,
            'value1' => $value1,
            'value2' => $value2,
            'separator' => $separator,
            'not' => $not,
        );
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
            if($value instanceof Closure){
                $expr = new Expression();
                $value($expr);
                $value = $expr;
            }

            $this->columns[] = [
                'column' => $column,
                'value' => $value,
            ];
        }
    }

    public function addOrder(array $columns, string $order, string  $nulls = null)
    {
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
     * @param array $columns
     */
    public function addGroupBy(array $columns)
    {
        $this->group = $columns;
    }

    /**
     * @param $column
     * @param null $alias
     */
    public function addColumn($column, $alias = null)
    {
        $this->columns[] = [
            'name' => $column,
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
        $this->values[] = $value;
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

}