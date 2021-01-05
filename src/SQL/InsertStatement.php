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

class InsertStatement
{
    protected ?SQLStatement $sql;

    public function __construct(?SQLStatement $statement = null)
    {
        $this->sql = $statement ?? new SQLStatement();
    }

    /**
     * @internal
     * @return SQLStatement
     */
    public function getSQLStatement(): SQLStatement
    {
        return $this->sql;
    }

    public function insert(array $values): static
    {
        foreach ($values as $column => $value) {
            $this->sql->addColumn($column);
            $this->sql->addValue($value);
        }

        return $this;
    }

    public function into(string $table): mixed
    {
        $this->sql->addTables([$table]);
        return null;
    }

    public function __clone()
    {
        $this->sql = clone $this->sql;
    }
}
