<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013 Marius Sarca
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

use PDO;
use Closure;
use Opis\Database\Database;
use Opis\Database\SQL\Raw;
use Opis\Database\SQL\Join;
use Opis\Database\SQL\Subquery;


class Query
{
    
    /** @var \Opis\Database\Database Database instance. */
    protected $database;

    /** @var \Opis\Database\Factory\Compiler Query compiler. */
    protected $compiler;

    /** @var mixed Database table. */
    protected $table;

    /** @var boolean Select distinct? */
    protected $distinct = false;

    /** @var array Columns from which we are fetching data. */
    protected $columns = array('*');

    /** @var array WHERE clauses. */
    protected $wheres = array();

    /** @var array JOIN clauses. */
    protected $joins = array();

    /** @var array GROUP BY clauses. */
    protected $groupings = array();

    /** @var array HAVING clauses. */
    protected $havings = array();

    /** @var array ORDER BY clauses. */
    protected $orderings = array();

    /** @var int Limit. */
    protected $limit = null;

    /** @var int Offset */
    protected $offset = null;


    /**
     * Constructor.
     *
     * @access  public
     * @param   \Opis\Database\Database   $database Database instance
     * @param   mixed                       $table      Database table or subquery
     */
    
    public function __construct(Database $database, $table)
    {
        $this->table = $table;
        $this->database = $database;
        $this->compiler = $database->getCompiler();
    }


    /**
     * Returns query compiler instance.
     * 
     * @access  public
     * @return  \Opis\Database\SQL\Compiler
     */

    public function getCompiler()
    {
        return $this->compiler;
    }

    /**
     * Returns the database table.
     * 
     * @access  public
     * @return  mixed
     */

    public function getTable()
    {
        return $this->table;
    }

    /**
     * Is it a distict select?
     * 
     * @access  public
     * @return  boolean
     */

    public function isDistinct()
    {
        return $this->distinct;
    }

    /**
     * Returns the columns from which we are fetching data.
     * 
     * @access  public
     * @return  array
     */

    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Returns WHERE clauses.
     * 
     * @access  public
     * @return  array
     */

    public function getWheres()
    {
        return $this->wheres;
    }

    /**
     * Returns JOIN clauses.
     * 
     * @access  public
     * @return  array
     */

    public function getJoins()
    {
        return $this->joins;
    }

    /**
     * Returns GROUP BY clauses.
     * 
     * @access  public
     * @return  array
     */

    public function getGroupings()
    {
        return $this->groupings;
    }

    /**
     * Returns HAVING clauses.
     * 
     * @access  public
     * @return  array
     */

    public function getHavings()
    {
        return $this->havings;
    }

    /**
     * Returns ORDER BY clauses.
     * 
     * @access  public
     * @return  array
     */

    public function getOrderings()
    {
        return $this->orderings;
    }

    /**
     * Returns the limit.
     * 
     * @access  public
     * @return  integer
     */

    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Returns the offset.
     * 
     * @access  public
     * @return  integer
     */

    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Sets the columns we want to select.
     *
     * @access  public
     * @param   array   $columns    Array of columns
     * @return  \Opis\Database\SQL\Query
     */

    public function columns(array $columns)
    {
        if(!empty($columns))
        {
            $this->columns = $columns;
        }
        return $this;
    }

    /**
     * Select distinct?
     *
     * @return  \Opis\Database\SQL\Query
     */

    public function distinct()
    {
        $this->distinct = true;
        return $this;
    }

    /**
     * Adds a WHERE clause.
     *
     * @access  public
     * @param   string|\Closure $column     Column name or closure
     * @param   string          $operator   (optional) Operator
     * @param   mixed           $value      (optional) Value
     * @param   string          $separator  (optional) Clause separator
     * @return  \Opis\Database\SQL\Query
     */

    public function where($column, $operator = null, $value = null, $separator = 'AND')
    {
        if($column instanceof Closure)
        {
            $query = new self($this->database, $this->table);
            $column($query);
            $this->wheres[] = array(
                'type'      => 'nestedWhere',
                'query'     => $query,
                'separator' => $separator,
            );
            
        }
        else
        {
            $this->wheres[] = array(
                'type'      => 'where',
                'column'    => $column,
                'operator'  => $operator,
                'value'     => $value,
                'separator' => $separator,
            );
        }
        return $this;
    }

    /**
     * Adds a OR WHERE clause.
     *
     * @access  public
     * @param   string|\Closure $column     Column name or closure
     * @param   string          $operator   (optional) Operator
     * @param   mixed           $value      (optional) Value
     * @return  \Opis\Database\SQL\Query
     */

    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    /**
     * Adds a BETWEEN clause.
     *
     * @access  public
     * @param   string  $column     Column name
     * @param   mixed   $value1     First value
     * @param   mixed   $value2     Second value
     * @param   string  $separator  (optional) Clause separator
     * @param   boolean $not        (optional) Not between
     * @return  \Opis\Database\SQL\Query
     */

    public function between($column, $value1, $value2, $separator = 'AND', $not = false)
    {
        $this->wheres[] = array(
            'type'      => 'between',
            'column'    => $column,
            'value1'    => $value1,
            'value2'    => $value2,
            'separator' => $separator,
            'not'       => $not,
        );
        return $this;
    }

    /**
     * Adds a OR BETWEEN clause.
     *
     * @access  public
     * @param   string  $column Column name
     * @param   mixed   $value1 First value
     * @param   mixed   $value2 Second value
     * @return  \Opis\Database\SQL\Query
     */

    public function orBetween($column, $value1, $value2)
    {
        return $this->between($column, $value1, $value2, 'OR');
    }

    /**
     * Adds a NOT BETWEEN clause.
     *
     * @access  public
     * @param   string  $column Column name
     * @param   mixed   $value1 First value
     * @param   mixed   $value2 Second value
     * @return  \Opis\Database\SQL\Query
     */

    public function notBetween($column, $value1, $value2)
    {
        return $this->between($column, $value1, $value2, 'AND', true);
    }

    /**
     * Adds a OR NOT BETWEEN clause.
     *
     * @access  public
     * @param   string  $column Column name
     * @param   mixed   $value1 First value
     * @param   mixed   $value2 Second value
     * @return  \Opis\Database\SQL\Query
     */

    public function orNotBetween($column, $value1, $value2)
    {
        return $this->between($column, $value1, $value2, 'OR', true);
    }

    /**
     * Adds a IN clause.
     *
     * @access  public
     * @param   string  $column     Column name
     * @param   mixed   $values     Array of values or Subquery
     * @param   string  $separator  (optional) Clause separator
     * @param   boolean $not        (optional) Not in
     * @return  \Opis\Database\SQL\Query
     */

    public function in($column, $values, $separator = 'AND', $not = false)
    {
        if($values instanceof Subquery)
        {
            $values = array($values);
        }
        $this->wheres[] = array(
            'type'      => 'in',
            'column'    => $column,
            'values'    => $values,
            'separator' => $separator,
            'not'       => $not,
        );
        return $this;
    }

    /**
     * Adds a OR IN clause.
     *
     * @access  public
     * @param   string  $column Column name
     * @param   mixed   $values Array of values or Subquery
     * @return  \Opis\Database\SQL\Query
     */

    public function orIn($column, $values)
    {
        return $this->in($column, $values, 'OR');
    }

    /**
     * Adds a NOT IN clause.
     *
     * @access  public
     * @param   string  $column Column name
     * @param   mixed   $values Array of values or Subquery
     * @return  \Opis\Database\SQL\Query
     */

    public function notIn($column, $values)
    {
        return $this->in($column, $values, 'AND', true);
    }

    /**
     * Adds a OR NOT IN clause.
     *
     * @access  public
     * @param   string  $column Column name
     * @param   mixed   $values Array of values or Subquery
     * @return  \Opis\Database\SQL\Query
     */

    public function orNotIn($column, $values)
    {
        return $this->in($column, $values, 'OR', true);
    }

    /**
     * Adds a IS NULL clause.
     *
     * @access  public
     * @param   mixed   $column     Column name
     * @param   string  $separator  (optional) Clause separator
     * @param   boolean $not        (optional) Not in
     * @return  \Opis\Database\SQL\Query
     */

    public function isNull($column, $separator = 'AND', $not = false)
    {
        $this->wheres[] = array(
            'type'      => 'isNull',
            'column'    => $column,
            'separator' => $separator,
            'not'       => $not,
        );
        return $this;
    }

    /**
     * Adds a OR IS NULL clause.
     *
     * @access  public
     * @param   mixed   $column Column name
     * @return  \Opis\Database\SQL\Query
     */

    public function orNull($column)
    {
        return $this->isNull($column, 'OR');
    }

    /**
     * Adds a IS NOT NULL clause.
     *
     * @access  public
     * @param   mixed   $column Column name
     * @return  \Opis\Database\SQL\Query
     */

    public function notNull($column)
    {
        return $this->isNull($column, 'AND', true);
    }

    /**
     * Adds a OR IS NOT NULL clause.
     *
     * @access  public
     * @param   mixed   $column Column name
     * @return  \Opis\Database\SQL\Query
     */

    public function orNotNull($column)
    {
        return $this->isNull($column, 'OR', true);
    }

    /**
     * Adds a EXISTS clause.
     *
     * @access  public
     * @param   \Opis\Database\SQL\Subquery $query      Subquery
     * @param   string                      $separator  (optional) Clause separator
     * @param   boolean                     $not        (optional)  Not exists
     * @return  \Opis\Database\SQL\Query
     */

    public function exists(Subquery $query, $separator = 'AND', $not = false)
    {
        $this->wheres[] = array(
            'type'      => 'exists',
            'query'     => $query,
            'separator' => $separator,
            'not'       => $not,
        );
        return $this;
    }

    /**
     * Adds a OR EXISTS clause.
     *
     * @access  public
     * @param   \Opis\Database\SQL\Subquery $query  Subquery
     * @return  \Opis\Database\SQL\Query
     */

    public function orExists(Subquery $query)
    {
        return $this->exists($query, 'OR');
    }

    /**
     * Adds a NOT EXISTS clause.
     *
     * @access  public
     * @param   \Opis\Database\SQL\Subquery $query  Subquery
     * @return  \Opis\Database\SQL\Query
     */

    public function notExists(Subquery $query)
    {
        return $this->exists($query, 'AND', true);
    }

    /**
     * Adds a OR NOT EXISTS clause.
     *
     * @access  public
     * @param   \Opis\Database\SQL\Subquery $query  Subquery
     * @return  \Opis\Database\SQL\Query
     */

    public function orNotExists(Subquery $query)
    {
        return $this->exists($query, 'or', true);
    }

    /**
     * Adds a JOIN clause.
     *
     * @access  public
     * @param   string          $table      Table name
     * @param   string|\Closure $column1    (optional) Column name or closure
     * @param   string          $operator   (optional) Operator
     * @param   string          $column2    (optional) Column name
     * @param   string          $type       (optional) Join type
     * @return  \Opis\Database\SQL\Query
     */

    public function join($table, $column1 = null, $operator = null, $column2 = null, $type = 'INNER')
    {
        $join = new Join($type, $table);
        if($column1 instanceof Closure)
        {
            $column1($join);
        }
        else
        {
            $join->on($column1, $operator, $column2);
        }
        $this->joins[] = $join;
        return $this;
    }

    /**
     * Adds a LEFT OUTER JOIN clause.
     *
     * @access  public
     * @param   string          $table      Table name
     * @param   string|\Closure $column1    (optional) Column name or closure
     * @param   string          $operator   (optional) Operator
     * @param   string          $column2    (optional) Column name
     * @return  \Opis\Database\SQL\Query
     */

    public function leftJoin($table, $column1 = null, $operator = null, $column2 = null)
    {
        return $this->join($table, $column1, $operator, $column2, 'LEFT OUTER');
    }

    /**
     * Adds a GROUP BY clause.
     *
     * @access  public
     * @param   string|array    $columns    Column name or array of column names
     * @return  \Opis\Database\SQL\Query
     */

    public function groupBy($columns)
    {
        if(!is_array($columns))
        {
            $columns = array($columns);
        }
        $this->groupings = $columns;
        return $this;
    }

    /**
     * Adds a HAVING clause.
     *
     * @access  public
     * @param   string  $column     Column name
     * @param   string  $operator   Operator
     * @param   mixed   $value      Value
     * @param   string  $separator  (optional) Clause separator
     * @return  \Opis\Database\SQL\Query
     */

    public function having($column, $operator, $value, $separator = 'AND')
    {
        $this->havings[] = array(
            'column'    => $column,
            'operator'  => $operator,
            'value'     => $value,
            'separator' => $separator,
        );
        return $this;
    }

    /**
     * Adds a OR HAVING clause.
     *
     * @access  public
     * @param   string  $column     Column name
     * @param   string  $operator   Operator
     * @param   mixed   $value      Value
     * @return  \Opis\Database\SQL\Query
     */

    public function orHaving($column, $operator, $value)
    {
        return $this->having($column, $operator, $value, 'OR');
    }

    /**
     * Adds a ORDER BY clause.
     *
     * @access  public
     * @param   string|array    $columns    Column name or array of column names
     * @param   string          $order      (optional) Sorting order
     * @return  \Opis\Database\SQL\Query
     */

    public function orderBy($columns, $order = 'ASC')
    {
        if(!is_array($columns))
        {
            $columns = array($columns);
        }
        $this->orderings[] = array(
            'column' => $columns,
            'order'  => $order,
        );
        return $this;
    }

    /**
     * Adds a ascending ORDER BY clause.
     *
     * @access  public
     * @param   string|array    $columns    Column name or array of column names
     * @return  \Opis\Database\SQL\Query
     */

    public function ascending($columns)
    {
        return $this->orderBy($columns, 'ASC');
    }

    /**
     * Adds a descending ORDER BY clause.
     *
     * @access  public
     * @param   string|array    $columns    Column name or array of column names
     * @return  \Opis\Database\SQL\Query
     */

    public function descending($columns)
    {
        return $this->orderBy($columns, 'DESC');
    }

    /**
     * Adds a LIMIT clause.
     *
     * @access  public
     * @param   integer $limit  Limit
     * @return  \Opis\Database\SQL\Query
     */

    public function limit($limit)
    {
        $this->limit = (int) $limit;
        return $this;
    }

    /**
     * Adds a OFFSET clause.
     *
     * @access  public
     * @param   int $offset Offset
     * @return  \Opis\Database\SQL\Query
     */

    public function offset($offset)
    {
        $this->offset = (int) $offset;
        return $this;
    }

    /**
     * Executes a SELECT query and returns an array containing all of the result set rows.
     *
     * @access  public
     * @param   array   $columns    (optional) Columns to select
     * @return  array
     */

    public function all(array $columns = array())
    {
        $this->columns($columns);
        $query = $this->compiler->select($this);
        return $this->database->all($query['sql'], $query['params']);
    }

    /**
     * Executes a SELECT query and returns the first row of the result set.
     *
     * @access  public
     * @param   array   $columns    (optional) Columns to select
     * @return  mixed
     */

    public function first(array $columns = array())
    {
        $this->columns($columns);
        $query = $this->compiler->select($this);
        return $this->database->first($query['sql'], $query['params']);
    }

    /**
     * Executes a SELECT query and returns the value of the chosen column of the first row of the result set.
     *
     * @access  public
     * @param   string   $column  Column to select
     * @return  mixed
     */

    public function column($column)
    {
        $this->columns(array($column));
        $query = $this->compiler->select($this);
        return $this->database->column($query['sql'], $query['params']);
    }

    /**
     * Executes a aggregate query and returns the result.
     *
     * @access  public
     * @param   string  $column     Column
     * @param   string  $function   Aggregate function
     * @return  mixed
     */

    protected function aggregate($column, $function)
    {
        return $this->column(new Raw($function . '(' . $this->compiler->wrap($column) . ')'));
    }

    /**
     * Returns the minimum value for the chosen column.
     *
     * @access  public
     * @param   string  $column Column name
     * @return  int
     */

    public function min($column)
    {
        return $this->aggregate($column, 'MIN');
    }

    /**
     * Returns the maximum value for the chosen column.
     *
     * @access  public
     * @param   string  $column  Column name
     * @return  int
     */

    public function max($column)
    {
        return $this->aggregate($column, 'MAX');
    }

    /**
     * Returns sum of all the values in the chosen column.
     *
     * @access  public
     * @param   string  $column  Column name
     * @return  int
     */

    public function sum($column)
    {
        return $this->aggregate($column, 'SUM');
    }

    /**
     * Returns the average value for the chosen column.
     *
     * @access  public
     * @param   string  $column  Column name
     * @return  float
     */

    public function avg($column)
    {
        return $this->aggregate($column, 'AVG');
    }

    /**
     * Returns the number of rows.
     *
     * @access  public
     * @param   string  $column (optional) Column name
     * @return  int
     */

    public function count($column = '*')
    {
        return $this->aggregate($column, 'COUNT');
    }

    /**
     * Inserts data into the chosen table.
     *
     * @access  public
     * @param   array   $values Associative array of column values
     * @return  boolean
     */

    public function insert(array $values)
    {
        $query = $this->compiler->insert($this, $values);
        return $this->database->insert($query['sql'], $query['params']);
    }

    /**
     * Updates data from the chosen table.
     *
     * @access  public
     * @param   array   $values Associative array of column values
     * @return  int
     */

    public function update(array $values)
    {
        $query = $this->compiler->update($this, $values);
        return $this->database->update($query['sql'], $query['params']);
    }

    /**
     * Increments column value.
     *
     * @access  public
     * @param   string  $column     Column name
     * @param   int     $increment  (optional) Increment value
     * @return  int
     */

    public function increment($column, $increment = 1)
    {
        return $this->update(array($column => new Raw($this->compiler->wrap($column) . ' + ' . (int) $increment)));
    }

    /**
     * Decrements column value.
     *
     * @access  public
     * @param   string  $column     Column name
     * @param   int     $decrement  (optional) Decrement value
     * @return  int
     */

    public function decrement($column, $decrement = 1)
    {
        return $this->update(array($column => new Raw($this->compiler->wrap($column) . ' - ' . (int) $decrement)));
    }

    /**
     * Deletes data from the chosen table.
     *
     * @access  public
     * @return  int
     */

    public function delete()
    {
        $query = $this->compiler->delete($this);
        return $this->database->delete($query['sql'], $query['params']);
    }
}