<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2015 Marius Sarca
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

use DateTime;

class Compiler
{
    /** @var    string  Date format. */
    protected $dateFormat = 'Y-m-d H:i:s';

    /** @var    string  Wrapper used to escape table and column names. */
    protected $wrapper = '"%s"';

    /** @var    array   Query params */
    protected $params = array();

    /**
     * Wrap a value
     * 
     * @param   mixed   $value
     * 
     * @return  string
     */
    protected function wrap($value)
    {
        if ($value instanceof Expression) {
            return $this->handleExpressions($value->getExpressions());
        }

        $wrapped = array();

        foreach (explode('.', $value) as $segment) {
            if ($segment == '*') {
                $wrapped[] = $segment;
            } else {
                $wrapped[] = sprintf($this->wrapper, $segment);
            }
        }

        return implode('.', $wrapped);
    }

    /**
     * Stores a query param
     * 
     * @param   mixed   $value
     * 
     * @return  string
     */
    protected function param($value)
    {
        if ($value instanceof Expression) {
            return $this->handleExpressions($value->getExpressions());
        } elseif ($value instanceof DateTime) {
            $this->params[] = $value->format($this->dateFormat);
        } else {
            $this->params[] = $value;
        }
        return '?';
    }

    /**
     * Sets compiler options
     * 
     * @param   array   $options
     */
    public function setOptions(array $options)
    {
        foreach ($options as $name => $value) {
            $this->{$name} = $value;
        }
    }

    /**
     * Stores an array of params
     * 
     * @param   array   $params
     * 
     * @return  string
     */
    public function params(array $params)
    {
        return implode(', ', array_map(array($this, 'param'), $params));
    }

    /**
     * Add an array of columns
     * 
     * @param   array   $columns
     * 
     * @return  string
     */
    public function columns(array $columns)
    {
        return implode(', ', array_map(array($this, 'wrap'), $columns));
    }

    /**
     * Return the stored params
     * 
     * @return array
     */
    public function getParams()
    {
        $params = $this->params;
        $this->params = array();
        return $params;
    }

    /**
     * Handle all expressions
     * 
     * @param   array   $expressions
     * 
     * @return string
     */
    protected function handleExpressions(array $expressions)
    {
        $sql = array();

        foreach ($expressions as $expr) {
            switch ($expr['type']) {
                case 'column':
                    $sql[] = $this->wrap($expr['value']);
                    break;
                case 'op':
                    $sql[] = $expr['value'];
                    break;
                case 'value':
                    $sql[] = $this->param($expr['value']);
                    break;
                case 'group':
                    $sql[] = '(' . $this->handleExpressions($expr['value']->getExpressions()) . ')';
                    break;
                case 'function':
                    $sql[] = $this->handleSqlFunction($expr['value']);
                    break;
                case 'subquery':
                    $sql[] = '(' . $expr['value'] . ')';
                    break;
            }
        }

        return implode(' ', $sql);
    }

    /**
     * Handle SQL functions
     * 
     * @param   array   $func
     * 
     * @return  string
     */
    protected function handleSqlFunction(array $func)
    {
        $method = $func['type'] . $func['name'];
        return $this->$method($func);
    }

    /**
     * Handle tables
     * 
     * @param   array   $tables
     * 
     * @return string
     */
    protected function handleTables(array $tables)
    {
        if (empty($tables)) {
            return '';
        }

        $sql = array();

        foreach ($tables as $name => $alias) {
            if (is_string($name)) {
                $sql[] = $this->wrap($name) . ' AS ' . $this->wrap($alias);
            } else {
                $sql[] = $this->wrap($alias);
            }
        }
        return implode(', ', $sql);
    }

    /**
     * Handle columns
     * 
     * @param   array   $columns
     * 
     * @return  string
     */
    protected function handleColumns(array $columns)
    {
        if (empty($columns)) {
            return '*';
        }

        $sql = array();

        foreach ($columns as $column) {
            if ($column['alias'] !== null) {
                $sql[] = $this->wrap($column['name']) . ' AS ' . $this->wrap($column['alias']);
            } else {
                $sql[] = $this->wrap($column['name']);
            }
        }
        return implode(', ', $sql);
    }

    /**
     * Handle INTO
     * 
     * @param   string  $table
     * @param   string  $database
     * 
     * @return  string
     */
    public function handleInto($table, $database)
    {
        if ($table === null) {
            return '';
        }
        return ' INTO ' . $this->wrap($table) . ($database === null ? '' : ' IN ' . $this->wrap($database));
    }

    /**
     * Handle WHERE conditions
     * 
     * @param   array   $wheres
     * @param   bool    $prefix (optional)
     * 
     * @return string
     */
    protected function handleWheres(array $wheres, $prefix = true)
    {
        if (empty($wheres)) {
            return '';
        }

        $sql[] = $this->{$wheres[0]['type']}($wheres[0]);

        $count = count($wheres);

        for ($i = 1; $i < $count; $i++) {
            $sql[] = $wheres[$i]['separator'] . ' ' . $this->{$wheres[$i]['type']}($wheres[$i]);
        }

        return ($prefix ? ' WHERE ' : '') . implode(' ', $sql);
    }

    /**
     * Handle groups
     * 
     * @param   array   $grouping
     * 
     * @return  string
     */
    protected function handleGroupings(array $grouping)
    {
        return empty($grouping) ? '' : ' GROUP BY ' . $this->columns($grouping);
    }

    /**
     * Handle JOIN clauses
     * 
     * @param   array   $joins
     * 
     * @return  string
     */
    protected function handleJoins(array $joins)
    {
        if (empty($joins)) {
            return '';
        }
        $sql = array();
        foreach ($joins as $join) {
            $sql[] = $join['type'] . ' JOIN ' . $this->handleTables($join['table']) . ' ON ' . $this->handleJoinCondtitions($join['join']->getJoinConditions());
        }
        return ' ' . implode(' ', $sql);
    }

    /**
     * Handle JOIN conditions
     * 
     * @param   array   $conditions
     * 
     * @return  string
     */
    protected function handleJoinCondtitions(array $conditions)
    {
        $sql[] = $this->{$conditions[0]['type']}($conditions[0]);
        $count = count($conditions);
        for ($i = 1; $i < $count; $i++) {
            $sql[] = $conditions[$i]['separator'] . ' ' . $this->{$conditions[$i]['type']}($conditions[$i]);
        }
        return implode(' ', $sql);
    }

    /**
     * Handle HAVING clause
     * 
     * @param   array   $havings
     * @param   bool    $prefix     (optional)
     * 
     * @return  string
     */
    protected function handleHavings(array $havings, $prefix = true)
    {
        if (empty($havings)) {
            return '';
        }

        $sql[] = $this->{$havings[0]['type']}($havings[0]);


        $count = count($havings);

        for ($i = 1; $i < $count; $i++) {
            $sql[] = $havings[$i]['separator'] . ' ' . $this->{$havings[$i]['type']}($havings[$i]);
        }

        return ($prefix ? ' HAVING ' : '') . implode(' ', $sql);
    }

    /**
     * Hanlde ORDER BY
     * 
     * @param   array   $ordering
     * 
     * @return  string
     */
    protected function handleOrderings(array $ordering)
    {
        if (empty($ordering)) {
            return '';
        }

        $sql = array();

        foreach ($ordering as $order) {
            if ($order['nulls'] !== null) {
                foreach ($order['columns'] as $column) {
                    $column = $this->columns(array($column));

                    if ($order['nulls'] == 'NULLS FIRST') {
                        $sql[] = '(CASE WHEN ' . $column . ' IS NULL THEN 0 ELSE 1 END)';
                    } else {
                        $sql[] = '(CASE WHEN ' . $column . ' IS NULL THEN 1 ELSE 0 END)';
                    }
                }
            }

            $sql[] = $this->columns($order['columns']) . ' ' . $order['order'];
        }

        return ' ORDER BY ' . implode(', ', $sql);
    }

    /**
     * Handle SET
     * 
     * @param   array   $columns
     * 
     * @return  string
     */
    protected function handleSetColumns(array $columns)
    {
        if (empty($columns)) {
            return '';
        }

        $sql = array();

        foreach ($columns as $column) {
            $sql[] = $this->wrap($column['column']) . ' = ' . $this->param($column['value']);
        }

        return ' SET ' . implode(', ', $sql);
    }

    /**
     * Hanlde insert values
     * 
     * @param   array   $values
     * 
     * @return  string
     */
    protected function handleInsertValues(array $values)
    {
        return ' VALUES (' . $this->params($values) . ')';
    }

    /**
     * Handle limits
     * 
     * @param   int|null    $limit
     * 
     * @return  string
     */
    protected function handleLimit($limit)
    {
        return ($limit === null) ? '' : ' LIMIT ' . $this->param($limit);
    }

    /**
     * Handle offsets
     * 
     * @param   int|null    $offset
     * 
     * @return  string
     */
    protected function handleOffset($offset)
    {
        return ($offset === null) ? '' : ' OFFSET ' . $this->param($offset);
    }

    /**
     * @param   array   $join
     * 
     * @return  string
     */
    protected function joinColumn(array $join)
    {
        return $this->wrap($join['column1']) . ' ' . $join['operator'] . ' ' . $this->wrap($join['column2']);
    }

    /**
     * @param   array    $join
     * 
     * @return  string
     */
    protected function joinNested(array $join)
    {
        return '(' . $this->handleJoinCondtitions($join['join']->getJoinCOnditions()) . ')';
    }

    /**
     * @param   array   $where
     * 
     * @return  string
     */
    protected function whereColumn(array $where)
    {
        return $this->wrap($where['column']) . ' ' . $where['operator'] . ' ' . $this->param($where['value']);
    }

    /**
     * @param   array   $where
     * 
     * @return  string
     */
    protected function whereIn(array $where)
    {
        return $this->wrap($where['column']) . ' ' . ($where['not'] ? 'NOT IN ' : 'IN ') . '(' . $this->params($where['value']) . ')';
    }

    /**
     * @param   array   $where
     * 
     * @return  string
     */
    protected function whereInSelect(array $where)
    {
        return $this->wrap($where['column']) . ' ' . ($where['not'] ? 'NOT IN ' : 'IN ') . '(' . $where['subquery'] . ')';
    }

    /**
     * @param   array   $where
     * 
     * @return  string
     */
    protected function whereNested(array $where)
    {
        return '(' . $this->handleWheres($where['clause']->getWhereConditions(), false) . ')';
    }

    /**
     * @param   array   $where
     * 
     * @return  string
     */
    protected function whereExists(array $where)
    {
        return ($where['not'] ? 'NOT EXISTS ' : 'EXISTS ') . '(' . $where['subquery'] . ')';
    }

    /**
     * @param   array   $where
     * 
     * @return  string
     */
    protected function whereNull(array $where)
    {
        return $this->wrap($where['column']) . ' ' . ($where['not'] ? 'IS NOT NULL' : 'IS NULL');
    }

    /**
     * @param   array   $where
     * 
     * @return  string
     */
    protected function whereBetween(array $where)
    {
        return $this->wrap($where['column']) . ' ' . ($where['not'] ? 'NOT BETWEEN' : 'BETWEEN') . ' ' . $this->param($where['value1']) . ' AND ' . $this->param($where['value2']);
    }

    /**
     * @param   array   $where
     * 
     * @return  string
     */
    protected function whereLike(array $where)
    {
        return $this->wrap($where['column']) . ' ' . ($where['not'] ? 'NOT LIKE' : 'LIKE') . ' ' . $this->param($where['pattern']);
    }

    /**
     * @param   array   $where
     * 
     * @return  string
     */
    protected function whereSubquery(array $where)
    {
        return $this->wrap($where['column']) . ' ' . $where['operator'] . ' (' . $where['subquery'] . ')';
    }

    /**
     * @param   array   $having
     * 
     * @return  string
     */
    protected function havingCondition(array $having)
    {
        return $this->wrap($having['aggregate']) . ' ' . $having['operator'] . ' ' . $this->param($having['value']);
    }

    /**
     * @param   array   $having
     * 
     * @return  string
     */
    protected function havingNested(array $having)
    {
        return '(' . $this->handleHavings($having['conditions'], false) . ')';
    }

    /**
     * @param   array   $having
     *
     * @return  string
     */
    protected function havingBetween(array $having)
    {
        return $this->wrap($having['aggregate']) . ($having['not'] ? ' NOT BETWEEN ' : ' BETWEEN ') . $this->param($having['value1']) . ' AND ' . $this->param($having['value2']);
    }

    /**
     * @param   array   $having
     *
     * @return  string
     */
    protected function havingInSelect(array $having)
    {
        return $this->wrap($having['aggregate']) . ($having['not'] ? ' NOT IN ' : ' IN ') . '(' . $having['subquery'] . ')';
    }

    /**
     * @param   array   $having
     *
     * @return  string
     */
    protected function havingIn(array $having)
    {
        return $this->wrap($having['aggregate']) . ($having['not'] ? ' NOT IN ' : ' IN ') . '(' . $this->params($having['value']) . ')';
    }

    /**
     * @param   array   $func
     *
     * @return  string
     */
    protected function aggregateFunctionCOUNT(array $func)
    {
        return 'COUNT(' . ($func['distinct'] ? 'DISTINCT ' : '') . $this->columns($func['column']) . ')';
    }

    /**
     * @param   array   $func
     * 
     * @return  string
     */
    protected function aggregateFunctionAVG(array $func)
    {
        return 'AVG(' . ($func['distinct'] ? 'DISTINCT ' : '') . $this->wrap($func['column']) . ')';
    }

    /**
     * @param   array   $func
     * 
     * @return  string
     */
    protected function aggregateFunctionSUM(array $func)
    {
        return 'SUM(' . ($func['distinct'] ? 'DISTINCT ' : '') . $this->wrap($func['column']) . ')';
    }

    /**
     * @param   array   $func
     * 
     * @return  string
     */
    protected function aggregateFunctionMIN(array $func)
    {
        return 'MIN(' . ($func['distinct'] ? 'DISTINCT ' : '') . $this->wrap($func['column']) . ')';
    }

    /**
     * @param   array   $func
     * 
     * @return  string
     */
    protected function aggregateFunctionMAX(array $func)
    {
        return 'MAX(' . ($func['distinct'] ? 'DISTINCT ' : '') . $this->wrap($func['column']) . ')';
    }

    /**
     * @param   array   $func
     * 
     * @return  string
     */
    protected function sqlFunctionUCASE(array $func)
    {
        return 'UCASE(' . $this->wrap($func['column']) . ')';
    }

    /**
     * @param   array   $func
     * 
     * @return string
     */
    protected function sqlFunctionLCASE(array $func)
    {
        return 'LCASE(' . $this->wrap($func['column']) . ')';
    }

    /**
     * @param   array   $func
     * 
     * @return  string
     */
    protected function sqlFunctionMID(array $func)
    {
        return 'MID(' . $this->wrap($func['column']) . ', ' . $this->param($func['start']) . ($func['lenght'] > 0 ? $this->param($func['lenght']) . ')' : ')');
    }

    /**
     * @param   array   $func
     * 
     * @return  string
     */
    protected function sqlFunctionLEN(array $func)
    {
        return 'LEN(' . $this->wrap($func['column']) . ')';
    }

    /**
     * @param   array   $func
     * 
     * @return  string
     */
    protected function sqlFunctionROUND(array $func)
    {
        return 'ROUND(' . $this->wrap($func['column']) . ', ' . $this->param($func['decimals']) . ')';
    }

    /**
     * @param   array   $func
     * 
     * @return  string
     */
    protected function sqlFunctionNOW(array $func)
    {
        return 'NOW()';
    }

    /**
     * @param   array    $func
     * 
     * @return  string
     */
    protected function sqlFunctionFORMAT(array $func)
    {
        return 'FORMAT(' . $this->wrap($func['column']) . ', ' . $this->param($func['format']) . ')';
    }

    /**
     * Returns the data format used
     * 
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * Returns the SQL for a select statement
     * 
     * @param   \Opis\Database\SQL\SelectStatement  $select
     * 
     * @return  string
     */
    public function select(SelectStatement $select)
    {
        $sql = $select->isDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
        $sql .= $this->handleColumns($select->getColumns());
        $sql .= $this->handleInto($select->getIntoTable(), $select->getIntoDatabase());
        $sql .= ' FROM ';
        $sql .= $this->handleTables($select->getTables());
        $sql .= $this->handleJoins($select->getJoinClauses());
        $sql .= $this->handleWheres($select->getWhereConditions());
        $sql .= $this->handleGroupings($select->getGroupClauses());
        $sql .= $this->handleOrderings($select->getOrderClauses());
        $sql .= $this->handleHavings($select->getHavingConditions());
        $sql .= $this->handleLimit($select->getLimit());
        $sql .= $this->handleOffset($select->getOffset());
        return $sql;
    }

    /**
     * Returns the SQL for an insert statement
     * 
     * @param   \Opis\Database\SQL\InsertStatement  $insert
     * 
     * @return  string
     */
    public function insert(InsertStatement $insert)
    {
        $columns = $this->handleColumns($insert->getColumns());

        $sql = 'INSERT INTO ';
        $sql .= $this->handleTables($insert->getTables());
        $sql .= ($columns === '*') ? '' : ' (' . $columns . ')';
        $sql .= $this->handleInsertValues($insert->getValues());

        return $sql;
    }

    /**
     * Returns the SQL for a update statement
     * 
     * @param   \Opis\Database\SQL\UpdateStatement  $update
     * 
     * @return  string
     */
    public function update(UpdateStatement $update)
    {
        $sql = 'UPDATE ';
        $sql .= $this->handleTables($update->getTables());
        $sql .= $this->handleJoins($update->getJoinClauses());
        $sql .= $this->handleSetColumns($update->getColumns());
        $sql .= $this->handleWheres($update->getWhereConditions());

        return $sql;
    }

    /**
     * Returns the SQL for a delete statement
     * 
     * @param   \Opis\Database\SQL\DeleteStatement  $delete
     * 
     * @return  string
     */
    public function delete(DeleteStatement $delete)
    {
        $sql = 'DELETE ' . $this->handleTables($delete->getTables());
        $sql .= $sql === 'DELETE ' ? 'FROM ' : ' FROM ';
        $sql .= $this->handleTables($delete->getFrom());
        $sql .= $this->handleJoins($delete->getJoinClauses());
        $sql .= $this->handleWheres($delete->getWhereConditions());

        return $sql;
    }
}
