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

use Closure;
use Opis\Database\SQL\{
    ColumnExpression, HavingStatement, SQLStatement
};

trait SelectTrait
{
    /**
     * @return SQLStatement
     */
    abstract protected function getSQLStatement(): SQLStatement;

    /**
     * @return HavingStatement
     */
    abstract protected function getHavingStatement(): HavingStatement;

    /**
     * @param mixed|array $columns
     * @return $this
     */
    public function select(mixed $columns = []): static
    {
        $expr = new ColumnExpression($this->getSQLStatement());

        if ($columns instanceof Closure) {
            $columns($expr);
        } else {
            if (!is_array($columns)) {
                $columns = [$columns];
            }
            $expr->columns($columns);
        }

        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function distinct(bool $value = true): static
    {
        $this->getSQLStatement()->setDistinct($value);
        return $this;
    }

    /**
     * @param mixed $columns
     * @return $this
     */
    public function groupBy(mixed $columns): static
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->getSQLStatement()->addGroupBy($columns);
        return $this;
    }

    /**
     * @param mixed $column
     * @param Closure|null $value
     * @return $this
     */
    public function having(mixed $column, ?Closure $value = null): static
    {
        $this->getHavingStatement()->having($column, $value);
        return $this;
    }

    /**
     * @param mixed $column
     * @param Closure|null $value
     * @return $this
     */
    public function andHaving(mixed $column, ?Closure $value = null): static
    {
        $this->getHavingStatement()->andHaving($column, $value);
        return $this;
    }

    /**
     * @param mixed $column
     * @param Closure|null $value
     * @return $this
     */
    public function orHaving(mixed $column, ?Closure $value = null): static
    {
        $this->getHavingStatement()->orHaving($column, $value);
        return $this;
    }

    /**
     * @param mixed $columns
     * @param string $order
     * @param string|null $nulls
     * @return $this
     */
    public function orderBy(mixed $columns, string $order = 'ASC', ?string $nulls = null): static
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->getSQLStatement()->addOrder($columns, $order, $nulls);
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function limit(int $value): static
    {
        $this->getSQLStatement()->setLimit($value);
        return $this;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function offset(int $value): static
    {
        $this->getSQLStatement()->setOffset($value);
        return $this;
    }
}