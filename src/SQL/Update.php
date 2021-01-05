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

use Opis\Database\Connection;

class Update extends UpdateStatement
{
    protected Connection $connection;

    public function __construct(Connection $connection, string|array $table, ?SQLStatement $statement = null)
    {
        parent::__construct($table, $statement);
        $this->connection = $connection;
    }

    public function increment(string|array $column, int $value = 1): int
    {
        return $this->incrementOrDecrement('+', $column, $value);
    }

    public function decrement(string|array $column, int $value = 1): int
    {
        return $this->incrementOrDecrement('-', $column, $value);
    }

    public function set(array $columns): int
    {
        parent::set($columns);
        $compiler = $this->connection->getCompiler();
        return $this->connection->count($compiler->update($this->sql), $compiler->getParams());
    }

    protected function incrementOrDecrement(string $sign, string|array $columns, int $value): int
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $values = [];

        foreach ($columns as $k => $v) {
            if (is_numeric($k)) {
                $values[$v] = static fn (Expression $expr) => $expr->column($v)->{$sign}->value($value);
            } else {
                $values[$k] = static fn (Expression $expr) => $expr->column($k)->{$sign}->value($v);
            }
        }

        return $this->set($values);
    }
}
