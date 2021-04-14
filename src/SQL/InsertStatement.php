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
    protected ?array $columns = null;

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

    public function insert(array ...$values): static
    {
        if (!$values) {
            return $this;
        }

        if ($this->columns === null) {
            // Generate column order
            $this->columns = [];
            foreach (array_keys(reset($values)) as $column) {
                if (is_string($column)) {
                    $this->columns[] = $column;
                    // Also add column to sql
                    $this->sql->addColumn($column);
                }
            }
        }

        if (!$this->columns) {
            // No columns
            return $this;
        }

        foreach ($values as $row) {
            $map = [];

            foreach ($this->columns as $column) {
                $map[] = $row[$column] ?? null;
            }

            $this->sql->addValues($map);
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
