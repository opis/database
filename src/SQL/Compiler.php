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

use DateTime;

class Compiler
{
    protected string $dateFormat = 'Y-m-d H:i:s';
    protected string $wrapper = '"%s"';
    protected array $params = [];


    public function select(SQLStatement $select): string
    {
        $sql = $select->getDistinct() ? 'SELECT DISTINCT ' : 'SELECT ';
        $sql .= $this->handleColumns($select->getColumns());
        $sql .= $this->handleInto($select->getIntoTable(), $select->getIntoDatabase());
        $sql .= ' FROM ';
        $sql .= $this->handleTables($select->getTables());
        $sql .= $this->handleJoins($select->getJoins());
        $sql .= $this->handleWheres($select->getWheres());
        $sql .= $this->handleGroupings($select->getGroupBy());
        $sql .= $this->handleOrderings($select->getOrder());
        $sql .= $this->handleHavings($select->getHaving());
        $sql .= $this->handleLimit($select->getLimit(), $select->getOffset());
        $sql .= $this->handleOffset($select->getOffset(), $select->getLimit());

        return $sql;
    }

    public function insert(SQLStatement $insert): string
    {
        $columns = $this->handleColumns($insert->getColumns());

        $sql = 'INSERT INTO ';
        $sql .= $this->handleTables($insert->getTables());
        $sql .= ($columns === '*') ? '' : ' (' . $columns . ')';
        $sql .= $this->handleInsertValues($insert->getValues());

        return $sql;
    }

    public function update(SQLStatement $update): string
    {
        $sql = 'UPDATE ';
        $sql .= $this->handleTables($update->getTables());
        $sql .= $this->handleJoins($update->getJoins());
        $sql .= $this->handleSetColumns($update->getColumns());
        $sql .= $this->handleWheres($update->getWheres());

        return $sql;
    }

    public function delete(SQLStatement $delete): string
    {
        $sql = 'DELETE ' . $this->handleTables($delete->getTables());
        $sql .= $sql === 'DELETE ' ? 'FROM ' : ' FROM ';
        $sql .= $this->handleTables($delete->getFrom());
        $sql .= $this->handleJoins($delete->getJoins());
        $sql .= $this->handleWheres($delete->getWheres());

        return $sql;
    }

    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    public function setOptions(array $options)
    {
        foreach ($options as $name => $value) {
            $this->{$name} = $value;
        }
    }

    public function params(array $params): string
    {
        return implode(', ', array_map([$this, 'param'], $params));
    }

    public function columns(array $columns): string
    {
        return implode(', ', array_map([$this, 'wrap'], $columns));
    }

    public function quote(string $value): string
    {
        return "'" . str_replace("'", "''", $value) . "'";
    }

    public function getParams(): array
    {
        $params = $this->params;
        $this->params = [];
        return $params;
    }

    protected function wrap(mixed $value): string
    {
        if ($value instanceof Expression) {
            return $this->handleExpressions($value->getExpressions());
        }

        $wrapped = [];

        foreach (explode('.', $value) as $segment) {
            if ($segment == '*') {
                $wrapped[] = $segment;
            } else {
                $wrapped[] = sprintf($this->wrapper, $segment);
            }
        }

        return implode('.', $wrapped);
    }

    protected function param(mixed $value): string
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

    protected function handleExpressions(array $expressions): string
    {
        $sql = [];

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
                    /** @var Expression $expression */
                    $expression = $expr['value'];
                    $sql[] = '(' . $this->handleExpressions($expression->getExpressions()) . ')';
                    break;
                case 'function':
                    $sql[] = $this->handleSqlFunction($expr['value']);
                    break;
                case 'call':
                    $sql[] = $this->handleCustomCall($expr['value']);
                    break;
                case 'subquery':
                    /** @var SubQuery $subquery */
                    $subquery = $expr['value'];
                    $sql[] = '(' . $this->select($subquery->getSQLStatement()) . ')';
                    break;
            }
        }

        return implode(' ', $sql);
    }

    protected function handleCustomCall(array $func): string
    {
        $args = array_map(function (mixed $arg) {
            if ($arg instanceof Expression) {
                return $this->handleExpressions($arg->getExpressions());
            }
            return $this->param($arg);
        }, $func['args']);

        if (!$args) {
            return $func['name'] . '()';
        }

        return $func['name'] . '(' . implode(', ', $args) . ')';
    }

    protected function handleSqlFunction(array $func): string
    {
        $method = $func['type'] . $func['name'];
        return $this->{$method}($func);
    }

    protected function handleTables(array $tables): string
    {
        if (empty($tables)) {
            return '';
        }

        $sql = [];

        foreach ($tables as $name => $alias) {
            if (is_string($name)) {
                $sql[] = $this->wrap($name) . ' AS ' . $this->wrap($alias);
            } else {
                $sql[] = $this->wrap($alias);
            }
        }
        return implode(', ', $sql);
    }

    protected function handleColumns(array $columns): string
    {
        if (empty($columns)) {
            return '*';
        }

        $sql = [];

        foreach ($columns as $column) {
            if ($column['alias'] !== null) {
                $sql[] = $this->wrap($column['name']) . ' AS ' . $this->wrap($column['alias']);
            } else {
                $sql[] = $this->wrap($column['name']);
            }
        }
        return implode(', ', $sql);
    }

    protected function handleInto(?string $table, ?string $database): string
    {
        if ($table === null) {
            return '';
        }
        return ' INTO ' . $this->wrap($table) . ($database === null ? '' : ' IN ' . $this->wrap($database));
    }

    protected function handleWheres(array $wheres, bool $prefix = true): string
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

    protected function handleGroupings(array $grouping): string
    {
        return empty($grouping) ? '' : ' GROUP BY ' . $this->columns($grouping);
    }

    protected function handleJoins(array $joins): string
    {
        if (empty($joins)) {
            return '';
        }
        $sql = [];
        foreach ($joins as $join) {
            /** @var Join $joinObject */
            $joinObject = $join['join'];

            $on = '';
            if ($joinObject) {
                $on = $this->handleJoinConditions($joinObject->getJoinConditions());
            }
            if ($on !== '') {
                $on = ' ON ' . $on;
            }

            $sql[] = $join['type'] . ' JOIN ' . $this->handleTables($join['table']) . $on;
        }
        return ' ' . implode(' ', $sql);
    }

    protected function handleJoinConditions(array $conditions): string
    {
        if (empty($conditions)) {
            return '';
        }
        $sql[] = $this->{$conditions[0]['type']}($conditions[0]);
        $count = count($conditions);
        for ($i = 1; $i < $count; $i++) {
            $sql[] = $conditions[$i]['separator'] . ' ' . $this->{$conditions[$i]['type']}($conditions[$i]);
        }
        return implode(' ', $sql);
    }

    protected function handleHavings(array $havings, bool $prefix = true): string
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

    protected function handleOrderings(array $ordering): string
    {
        if (empty($ordering)) {
            return '';
        }

        $sql = [];

        foreach ($ordering as $order) {
            if ($order['nulls'] !== null) {
                foreach ($order['columns'] as $column) {
                    $column = $this->columns([$column]);

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

    protected function handleSetColumns(array $columns): string
    {
        if (empty($columns)) {
            return '';
        }

        $sql = [];

        foreach ($columns as $column) {
            $sql[] = $this->wrap($column['column']) . ' = ' . $this->param($column['value']);
        }

        return ' SET ' . implode(', ', $sql);
    }

    protected function handleInsertValues(array $values): string
    {
        return ' VALUES (' . implode('), (', array_map([$this, 'params'], $values)) . ')';
    }

    /** @noinspection PhpUnusedParameterInspection */
    protected function handleLimit(?int $limit, ?int $offset): string
    {
        return ($limit === null) ? '' : ' LIMIT ' . $this->param($limit);
    }

    /** @noinspection PhpUnusedParameterInspection */
    protected function handleOffset(?int $offset, ?int $limit): string
    {
        return ($offset === null) ? '' : ' OFFSET ' . $this->param($offset);
    }

    protected function joinColumn(array $join): string
    {
        return $this->wrap($join['column1']) . ' ' . $join['operator'] . ' ' . $this->wrap($join['column2']);
    }

    protected function joinNested(array $join): string
    {
        return '(' . $this->handleJoinConditions($join['join']->getJoinConditions()) . ')';
    }

    protected function joinExpression(array $join): string
    {
        return $this->wrap($join['expression']);
    }

    protected function whereColumn(array $where): string
    {
        return $this->wrap($where['column']) . ' ' . $where['operator'] . ' ' . $this->param($where['value']);
    }

    protected function whereIn(array $where): string
    {
        return $this->wrap($where['column']) . ' ' . ($where['not'] ? 'NOT IN ' : 'IN ') . '(' . $this->params($where['value']) . ')';
    }

    protected function whereInSelect(array $where): string
    {
        return $this->wrap($where['column']) . ' ' . ($where['not'] ? 'NOT IN ' : 'IN ') . '(' . $this->select($where['subquery']->getSQLStatement()) . ')';
    }

    protected function whereNested(array $where): string
    {
        return '(' . $this->handleWheres($where['clause'], false) . ')';
    }

    protected function whereExists(array $where): string
    {
        return ($where['not'] ? 'NOT EXISTS ' : 'EXISTS ') . '(' . $this->select($where['subquery']->getSQLStatement()) . ')';
    }

    protected function whereNull(array $where): string
    {
        return $this->wrap($where['column']) . ' ' . ($where['not'] ? 'IS NOT NULL' : 'IS NULL');
    }

    protected function whereBetween(array $where): string
    {
        return $this->wrap($where['column']) . ' ' . ($where['not'] ? 'NOT BETWEEN' : 'BETWEEN') . ' ' . $this->param($where['value1']) . ' AND ' . $this->param($where['value2']);
    }

    protected function whereLike(array $where): string
    {
        return $this->wrap($where['column']) . ' ' . ($where['not'] ? 'NOT LIKE' : 'LIKE') . ' ' . $this->param($where['pattern']);
    }

    protected function whereSubQuery(array $where): string
    {
        return $this->wrap($where['column']) . ' ' . $where['operator'] . ' (' . $this->select($where['subquery']->getSQLStatement()) . ')';
    }

    /**
     * @param array $where
     *
     * @return string
     */
    protected function whereNop(array $where): string
    {
        return $this->wrap($where['column']);
    }

    protected function havingCondition(array $having): string
    {
        return $this->wrap($having['aggregate']) . ' ' . $having['operator'] . ' ' . $this->param($having['value']);
    }

    protected function havingNested(array $having): string
    {
        return '(' . $this->handleHavings($having['conditions'], false) . ')';
    }

    protected function havingBetween(array $having): string
    {
        return $this->wrap($having['aggregate']) . ($having['not'] ? ' NOT BETWEEN ' : ' BETWEEN ') . $this->param($having['value1']) . ' AND ' . $this->param($having['value2']);
    }

    protected function havingInSelect(array $having): string
    {
        return $this->wrap($having['aggregate']) . ($having['not'] ? ' NOT IN ' : ' IN ') . '(' . $this->select($having['subquery']->getSQLStatement()) . ')';
    }

    protected function havingIn(array $having): string
    {
        return $this->wrap($having['aggregate']) . ($having['not'] ? ' NOT IN ' : ' IN ') . '(' . $this->params($having['value']) . ')';
    }

    protected function aggregateFunctionCOUNT(array $func): string
    {
        return 'COUNT(' . ($func['distinct'] ? 'DISTINCT ' : '') . $this->columns($func['column']) . ')';
    }

    protected function aggregateFunctionAVG(array $func): string
    {
        return 'AVG(' . ($func['distinct'] ? 'DISTINCT ' : '') . $this->wrap($func['column']) . ')';
    }

    protected function aggregateFunctionSUM(array $func): string
    {
        return 'SUM(' . ($func['distinct'] ? 'DISTINCT ' : '') . $this->wrap($func['column']) . ')';
    }

    protected function aggregateFunctionMIN(array $func): string
    {
        return 'MIN(' . ($func['distinct'] ? 'DISTINCT ' : '') . $this->wrap($func['column']) . ')';
    }

    protected function aggregateFunctionMAX(array $func): string
    {
        return 'MAX(' . ($func['distinct'] ? 'DISTINCT ' : '') . $this->wrap($func['column']) . ')';
    }

    protected function sqlFunctionUCASE(array $func): string
    {
        return 'UCASE(' . $this->wrap($func['column']) . ')';
    }

    protected function sqlFunctionLCASE(array $func): string
    {
        return 'LCASE(' . $this->wrap($func['column']) . ')';
    }

    protected function sqlFunctionMID(array $func): string
    {
        return 'MID(' . $this->wrap($func['column']) . ', ' . $this->param($func['start']) . ($func['length'] > 0 ? $this->param($func['length']) . ')' : ')');
    }

    protected function sqlFunctionLEN(array $func): string
    {
        return 'LEN(' . $this->wrap($func['column']) . ')';
    }

    protected function sqlFunctionROUND(array $func): string
    {
        return 'ROUND(' . $this->wrap($func['column']) . ', ' . $this->param($func['decimals']) . ')';
    }

    /** @noinspection PhpUnusedParameterInspection */
    protected function sqlFunctionNOW(array $func): string
    {
        return 'NOW()';
    }

    protected function sqlFunctionFORMAT(array $func): string
    {
        return 'FORMAT(' . $this->wrap($func['column']) . ', ' . $this->param($func['format']) . ')';
    }
}
