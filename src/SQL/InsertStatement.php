<?php
/* ===========================================================================
 * Copyright 2018 Zindex Software
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

    /** @var  SQLStatement */
    protected $sql;

    /** @var string[]|null The column list, fixed by the first inserted row */
    protected $insertColumns;

    /**
     * InsertStatement constructor.
     * @param SQLStatement|null $statement
     */
    public function __construct(SQLStatement $statement = null)
    {
        if ($statement === null) {
            $statement = new SQLStatement();
        }
        $this->sql = $statement;
    }

    /**
     * @internal
     * @return SQLStatement
     */
    public function getSQLStatement(): SQLStatement
    {
        return $this->sql;
    }

    /**
     * Adds one or more rows of values to the statement.
     *
     * Each argument is handled independently: it may be a single row, given as a
     * column-value map, or a list of such rows, exactly like a single call with
     * {@see insert()} would treat it.
     *
     * @param array $values A single row, given as a column-value map, or a list of such rows
     * @param array ...$rows Additional rows, each given the same way as $values
     * @return InsertStatement
     * @throws \InvalidArgumentException If a row does not match the columns of the first row
     */
    public function insert(array $values, array ...$rows): self
    {
        array_unshift($rows, $values);

        foreach ($rows as $argument) {
            if ($this->holdsMultipleRows($argument)) {
                foreach ($argument as $row) {
                    $this->addRow($row);
                }
            } else {
                $this->addRow($argument);
            }
        }

        return $this;
    }

    /**
     * @param   string $table
     */
    public function into(string $table)
    {
        $this->sql->addTables([$table]);
    }

    /**
     * @inheritDoc
     */
    public function __clone()
    {
        $this->sql = clone $this->sql;
    }

    /**
     * Tells whether the given argument holds multiple rows instead of a single one.
     *
     * @param array $values
     * @return bool
     */
    private function holdsMultipleRows(array $values): bool
    {
        if ($values === []) {
            return false;
        }

        foreach ($values as $row) {
            if (!is_array($row)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Registers a single row, fixing the statement's column list on the first one.
     *
     * An empty row contributes nothing: no columns and no values row.
     *
     * @param array $row
     * @return void
     * @throws \InvalidArgumentException If the row does not match the fixed column list
     */
    private function addRow(array $row)
    {
        if ($row === []) {
            return;
        }

        if ($this->insertColumns === null) {
            $this->insertColumns = array_keys($row);

            foreach ($this->insertColumns as $column) {
                $this->sql->addColumn($column);
            }

            $this->sql->addValues(array_values($row));

            return;
        }

        $this->sql->addValues($this->alignRow($row));
    }

    /**
     * Validates a row against the statement's column list and reorders its values accordingly.
     *
     * The row number reported in any thrown exception is 1-based and reflects the row's
     * position within the whole statement, counting up across chained {@see insert()} calls.
     *
     * @param array $row
     * @return array The row's values, ordered like the statement's column list
     * @throws \InvalidArgumentException If the row misses a column or holds an unknown one
     */
    private function alignRow(array $row): array
    {
        $index = count($this->sql->getValueRows()) + 1;
        $values = [];

        foreach ($this->insertColumns as $column) {
            if (!array_key_exists($column, $row)) {
                throw new \InvalidArgumentException(
                    sprintf('Row %d is missing the column "%s"', $index, $column)
                );
            }

            $values[] = $row[$column];
        }

        foreach ($row as $column => $value) {
            if (!in_array($column, $this->insertColumns, true)) {
                throw new \InvalidArgumentException(
                    sprintf('Row %d contains an unknown column "%s"', $index, $column)
                );
            }
        }

        return $values;
    }
}
