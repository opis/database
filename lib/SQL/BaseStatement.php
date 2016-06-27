<?php
/**
 * Created by PhpStorm.
 * User: mari
 * Date: 26.06.2016
 * Time: 12:55
 */

namespace Opis\Database\SQL;


class BaseStatement
{
    /** @var  SQLStatement */
    protected $sql;

    /** @var  Where */
    protected $where;

    /**
     * BaseStatement constructor.
     */
    public function __construct()
    {
        $this->sql = new SQLStatement();
        $this->where = new Where($this, $this->sql);
    }

    /**
     * @param $column
     * @param $separator
     * @return $this|Where
     */
    protected function addWhereCondition($column, $separator)
    {
        if($column instanceof  Closure) {
            $this->sql->addWhereConditionGroup($column, 'AND');
            return $this;
        }
        return $this->where->init($column, $separator);
    }

    /**
     * @param $column
     * @return $this|Where
     */
    public function where($column)
    {
        return $this->addWhereCondition($column, 'AND');
    }

    /**
     * @param $column
     * @return $this|Where
     */
    public function andWhere($column)
    {
        return $this->addWhereCondition($column, 'AND');
    }

    /**
     * @param $column
     * @return $this|Where
     */
    public function orWhere($column)
    {
        return $this->addWhereCondition($column, 'OR');
    }

    /**
     * @param Closure $select
     * @return BaseStatement|static
     */
    public function whereExists(Closure $select): self
    {
        $this->getSQLStatement()->addWhereExistsCondition($select, 'AND', false);
        return $this;
    }

    /**
     * @param Closure $select
     * @return BaseStatement|static
     */
    public function andWhereExists(Closure $select): self
    {
        return $this->andWhereExists($select);
    }

    /**
     * @param Closure $select
     * @return BaseStatement|static
     */
    public function orWhereExists(Closure $select): self
    {
        $this->getSQLStatement()->addWhereExistsCondition($select, 'OR', false);
        return $this;
    }

    /**
     * @param Closure $select
     * @return BaseStatement|static
     */
    public function whereNotExists(Closure $select): self
    {
        $this->getSQLStatement()->addWhereExistsCondition($select, 'AND', true);
        return $this;
    }

    /**
     * @param Closure $select
     * @return BaseStatement|static
     */
    public function andWhereNotExists(Closure $select): self
    {
        return $this->andWhereNotExists($select);
    }

    /**
     * @param Closure $select
     * @return BaseStatement|static
     */
    public function orWhereNotExists(Closure $select): self
    {
        $this->getSQLStatement()->addWhereExistsCondition($select, 'OR', true);
        return $this;
    }

    /**
     * @param   string  $table
     * @param   Closure $closure
     *
     * @return  $this
     */
    public function join($table, Closure $closure)
    {
        $this->sql->addJoinClause('INNER', $table, $closure);
        return $this;
    }

    /**
     * @param   string  $table
     * @param   Closure $closure
     *
     * @return  $this
     */
    public function leftJoin($table, Closure $closure)
    {
        $this->sql->addJoinClause('LEFT', $table, $closure);
        return $this;
    }

    /**
     * @param   string  $table
     * @param   Closure $closure
     *
     * @return  $this
     */
    public function rightJoin($table, Closure $closure)
    {
        $this->sql->addJoinClause('RIGHT', $table, $closure);
        return $this;
    }

    /**
     * @param   string  $table
     * @param   Closure $closure
     *
     * @return  $this
     */
    public function fullJoin($table, Closure $closure)
    {
        $this->sql->addJoinClause('FULL', $table, $closure);
        return $this;
    }

}