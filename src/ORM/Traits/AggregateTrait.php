<?php
/* ===========================================================================
 * Copyright 2018-2020 Zindex Software
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

namespace Opis\Database\ORM\Traits;

use Opis\Database\SQL\{
    ColumnExpression, SQLStatement
};

trait AggregateTrait
{
    abstract protected function getSQLStatement(): SQLStatement;
    abstract protected function executeAggregate(): mixed;

    public function column(string $name): mixed
    {
        (new ColumnExpression($this->getSQLStatement()))->column($name);
        return $this->executeAggregate();
    }

    public function count(mixed $column = '*', bool $distinct = false): int
    {
        (new ColumnExpression($this->getSQLStatement()))->count($column, null, $distinct);
        return $this->executeAggregate();
    }

    public function avg(mixed $column, bool $distinct = false): int|float
    {
        (new ColumnExpression($this->getSQLStatement()))->avg($column, null, $distinct);
        return $this->executeAggregate();
    }

    public function sum(mixed $column, bool $distinct = false): int|float
    {
        (new ColumnExpression($this->getSQLStatement()))->sum($column, null, $distinct);
        return $this->executeAggregate();
    }

    public function min(mixed $column, bool $distinct = false): int|float
    {
        (new ColumnExpression($this->getSQLStatement()))->min($column, null, $distinct);
        return $this->executeAggregate();
    }

    public function max(mixed $column, bool $distinct = false): int|float
    {
        (new ColumnExpression($this->getSQLStatement()))->max($column, null, $distinct);
        return $this->executeAggregate();
    }
}