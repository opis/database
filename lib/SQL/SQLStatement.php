<?php
/**
 * Created by PhpStorm.
 * User: msarca
 * Date: 24.06.2016
 * Time: 12:55
 */

namespace Opis\Database\SQL;

use Closure;

class SQLStatement
{
    protected $wheres = [];
    protected $having = [];
    protected $joins = [];

    /**
     * @param Closure $callback
     * @param $separator
     * @return $this
     */
    public function addWhereConditionGroup(Closure $callback, $separator)
    {
        $condition = new WhereCondition();
        $callback($condition);
        $this->wheres[] = array(
            'type' => 'whereNested',
            'clause' => $condition->getWhereClause(),
            'separator' => $separator
        );
        return $this;
    }

    /**
     * @param string $column
     * @param $value
     * @param string $operator
     * @param string $separator
     * @return $this
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

        return $this;
    }

    /**
     * @param $column
     * @param $pattern
     * @param $separator
     * @param $not
     * @return $this
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
        return $this;
    }

    /**
     * @param string $column
     * @param $value1
     * @param $value2
     * @param string $separator
     * @param bool $not
     * @return $this
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

        return $this;
    }
    /**
     * @param $column
     * @param $value
     * @param $separator
     * @param $not
     * @return $this
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
        return $this;
    }

    /**
     * @param string $column
     * @param string $separator
     * @param bool $not
     * @return $this
     */
    public function addWhereNullCondition(string $column, string $separator, bool $not)
    {
        $this->wheres[] = array(
            'type' => 'whereNull',
            'column' => $column,
            'separator' => $separator,
            'not' => $not,
        );
        return $this;
    }

    /**
     * @param $closure
     * @param $separator
     * @param $not
     * @return $this
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

        return $this;
    }

    /**
     *  @param  string          $type
     *  @param  string|array    $table
     *  @param  Closure         $closure
     *
     *  @return $this
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

        return $this;
    }
    /**
     * @param   Closure $callback
     * @param   string  $separator
     */
    public function addHavingGroupCondition(Closure $callback, string $separator)
    {
        $having = new HavingCondition();
        $callback($having);

        $this->having[] = array(
            'type' => 'havingNested',
            'conditions' => $having->getHavingConditions(),
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
            'seperator' => $separator,
            'not' => $not,
        );
    }


}