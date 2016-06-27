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

trait WhereTrait
{
    abstract protected function getSQLStatement(): SQLStatement;

    abstract protected function getWhereCondition(string $column, string $separator): Where;

    /**
     * @param $column
     * @return self|Where
     */
    public function where($column)
    {
        if($column instanceof  Closure) {
            $this->getSQLStatement()->addWhereConditionGroup($column, 'AND');
            return $this;
        }

        return $this->getWhereCondition($column, 'AND');
    }

    /**
     * @param $column
     * @return self|Where
     */
    public function andWhere($column)
    {
        return $this->where($column);
    }

    /**
     * @param $column
     * @return self|Where
     */
    public function orWhere($column)
    {
        return $this->getWhereCondition($column, 'OR');
    }

    /**
     * @param Closure $select
     * @return self
     */
    public function whereExists(Closure $select): self
    {
        $this->getSQLStatement()->addWhereExistsCondition($select, 'AND', false);
        return $this;
    }

    /**
     * @param Closure $select
     * @return self
     */
    public function andWhereExists(Closure $select): self
    {
        return $this->andWhereExists($select);
    }

    /**
     * @param Closure $select
     * @return self
     */
    public function orWhereExists(Closure $select): self
    {
        $this->getSQLStatement()->addWhereExistsCondition($select, 'OR', false);
        return $this;
    }

    /**
     * @param Closure $select
     * @return self
     */
    public function whereNotExists(Closure $select): self
    {
        $this->getSQLStatement()->addWhereExistsCondition($select, 'AND', true);
        return $this;
    }

    /**
     * @param Closure $select
     * @return self
     */
    public function andWhereNotExists(Closure $select): self
    {
        return $this->andWhereNotExists($select);
    }

    /**
     * @param Closure $select
     * @return self
     */
    public function orWhereNotExists(Closure $select): self
    {
        $this->getSQLStatement()->addWhereExistsCondition($select, 'OR', true);
        return $this;
    }
}