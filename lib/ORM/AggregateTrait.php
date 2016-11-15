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

namespace Opis\Database\ORM;

use Opis\Database\SQL\ColumnExpression;
use Opis\Database\SQL\SQLStatement;

trait AggregateTrait
{
    /**
     * @return SQLStatement
     */
    abstract protected function getSQLStatement(): SQLStatement;

    /**
     * @return mixed
     */
    abstract protected function executeAggregate();

    /**
     * @param   string $name
     * @return mixed
     */
    public function column(string $name)
    {
        (new ColumnExpression($this->getSQLStatement()))->column($name);
        return $this->executeAggregate();
    }

    /**
     * @param   string $column (optional)
     * @param   bool $distinct (optional)
     * @return mixed
     */
    public function count($column = '*', bool $distinct = false)
    {
        (new ColumnExpression($this->getSQLStatement()))->count($column, null, $distinct);
        return $this->executeAggregate();
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     * @return mixed
     */
    public function avg(string $column, bool $distinct = false)
    {
        (new ColumnExpression($this->getSQLStatement()))->avg($column, null, $distinct);
        return $this->executeAggregate();
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     * @return mixed
     */
    public function sum(string $column, bool $distinct = false)
    {
        (new ColumnExpression($this->getSQLStatement()))->sum($column, null, $distinct);
        return $this->executeAggregate();
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     * @return mixed
     */
    public function min(string $column, bool $distinct = false)
    {
        (new ColumnExpression($this->getSQLStatement()))->min($column, null, $distinct);
        return $this->executeAggregate();
    }

    /**
     * @param   string $column
     * @param   bool $distinct (optional)
     * @return mixed
     */
    public function max(string $column, bool $distinct = false)
    {
        (new ColumnExpression($this->getSQLStatement()))->max($column, null, $distinct);
        return $this->executeAggregate();
    }
}