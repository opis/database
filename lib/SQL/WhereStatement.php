<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2016 Marius Sarca
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

class WhereStatement
{
    /** @var SQLStatement  */
    protected $sql;

    /** @var Where  */
    protected $where;

    /**
     * WhereStatement constructor.
     * @param SQLStatement|null $statement
     */
    public function __construct(SQLStatement $statement = null)
    {
        if($statement === null){
            $statement = new SQLStatement();
        }

        $this->sql = $statement;
        $this->where = new Where($this, $statement);
    }

    /**
     * @param $column
     * @param string $separator
     * @return $this|Where
     */
    protected function addWhereCondition($column, string $separator ='AND')
    {
        if($column instanceof  Closure) {
            $this->sql->addWhereConditionGroup($column, $separator);
            return $this;
        }
        return $this->where->init($column, $separator);
    }

    /**
     * @param Closure $select
     * @param string $separator
     * @param bool $not
     * @return BaseStatement
     */
    protected function addWhereExistCondition(Closure $select, string $separator = 'AND', bool $not = false): self
    {
        $this->sql->addWhereExistsCondition($select, $separator, $not);
        return $this;
    }

    /**
     * @return SQLStatement
     */
    public function getSQLStatement(): SQLStatement
    {
        return $this->sql;
    }

    /**
     * @param $column
     * @return self|Where
     */
    public function where($column)
    {
        return $this->addWhereCondition($column);
    }

    /**
     * @param $column
     * @return self|Where
     */
    public function andWhere($column)
    {
        return $this->addWhereCondition($column);
    }

    /**
     * @param $column
     * @return self|Where
     */
    public function orWhere($column)
    {
        return $this->addWhereCondition($column, 'OR');
    }

    /**
     * @param Closure $select
     * @return self
     */
    public function whereExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select);
    }

    /**
     * @param Closure $select
     * @return self
     */
    public function andWhereExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select);
    }

    /**
     * @param Closure $select
     * @return self
     */
    public function orWhereExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select, 'OR');
    }

    /**
     * @param Closure $select
     * @return self
     */
    public function whereNotExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select, 'AND', true);
    }

    /**
     * @param Closure $select
     * @return self
     */
    public function andWhereNotExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select, 'AND', true);
    }

    /**
     * @param Closure $select
     * @return self
     */
    public function orWhereNotExists(Closure $select): self
    {
        return $this->addWhereExistCondition($select, 'OR', true);
    }
}