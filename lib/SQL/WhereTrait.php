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

    public function where($column)
    {
        return $this->andWhere($column);
    }

    /**
     * @param $column
     * @return $this|Where
     */
    public function andWhere($column)
    {
        if($column instanceof  Closure) {
            $this->getSQLStatement()->addWhereConditionGroup($column, 'AND');
            return $this;
        }
    }

    public function orWhere(string $column)
    {

    }

    public function whereExists(Closure $select): self
    {
        return $this->andWhereExists($select);
    }

    public function andWhereExists(Closure $select): self
    {
        $this->getSQLStatement()->addWhereExistsCondition($select, 'AND', false);
        return $this;
    }

    public function orWhereExists(Closure $select): self
    {
        $this->getSQLStatement()->addWhereExistsCondition($select, 'OR', false);
        return $this;
    }

    public function whereNotExists(Closure $select): self
    {
        return $this->andWhereNotExists($select);
    }

    public function andWhereNotExists(Closure $select): self
    {
        $this->getSQLStatement()->addWhereExistsCondition($select, 'AND', true);
        return $this;
    }

    public function orWhereNotExists(Closure $select): self
    {
        $this->getSQLStatement()->addWhereExistsCondition($select, 'OR', true);
        return $this;
    }
}