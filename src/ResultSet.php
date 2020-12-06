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

namespace Opis\Database;

use PDO;
use PDOStatement;

class ResultSet
{
    protected PDOStatement $statement;

    public function __construct(PDOStatement $statement)
    {
        $this->statement = $statement;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->statement->closeCursor();
    }

    /**
     * Count affected rows
     *
     * @return  int
     */
    public function count(): int
    {
        return $this->statement->rowCount();
    }

    /**
     * Fetch all results
     *
     * @param callable|null $callable
     * @param int $fetchStyle
     * @return array
     */
    public function all(callable $callable = null, int $fetchStyle = 0): array
    {
        if ($callable === null) {
            return $this->statement->fetchAll($fetchStyle);
        }

        return $this->statement->fetchAll($fetchStyle | PDO::FETCH_FUNC, $callable);
    }

    /**
     * @param bool $uniq
     * @param callable|null $callable
     * @return array
     */
    public function allGroup(bool $uniq = false, callable $callable = null): array
    {
        $fetchStyle = PDO::FETCH_GROUP | ($uniq ? PDO::FETCH_UNIQUE : 0);

        if ($callable === null) {
            return $this->statement->fetchAll($fetchStyle);
        }

        return $this->statement->fetchAll($fetchStyle | PDO::FETCH_FUNC, $callable);
    }

    /**
     * Fetch first result
     *
     * @param callable|null $callable
     * @return mixed
     */
    public function first(callable $callable = null): mixed
    {
        if ($callable !== null) {
            $result = $this->statement->fetch(PDO::FETCH_ASSOC);
            $this->statement->closeCursor();
            if (is_array($result)) {
                $result = call_user_func_array($callable, $result);
            }
        } else {
            $result = $this->statement->fetch();
            $this->statement->closeCursor();
        }

        return $result;
    }

    /**
     * Fetch next result
     *
     * @return mixed
     */
    public function next(): mixed
    {
        return $this->statement->fetch();
    }

    /**
     * Close current cursor
     *
     * @return bool
     */
    public function flush(): bool
    {
        return $this->statement->closeCursor();
    }

    /**
     * Return a column
     *
     * @param int $col
     * @return mixed
     */
    public function column(int $col = 0): mixed
    {
        return $this->statement->fetchColumn($col);
    }

    /**
     * Fetch each result as an associative array
     *
     * @return $this
     */
    public function fetchAssoc(): static
    {
        $this->statement->setFetchMode(PDO::FETCH_ASSOC);
        return $this;
    }

    /**
     * Fetch each result as an stdClass object
     *
     * @return $this
     */
    public function fetchObject(): static
    {
        $this->statement->setFetchMode(PDO::FETCH_OBJ);
        return $this;
    }

    /**
     * @return $this
     */
    public function fetchNamed(): static
    {
        $this->statement->setFetchMode(PDO::FETCH_NAMED);
        return $this;
    }

    /**
     * @return $this
     */
    public function fetchNum(): static
    {
        $this->statement->setFetchMode(PDO::FETCH_NUM);
        return $this;
    }

    /**
     * @return $this
     */
    public function fetchBoth(): static
    {
        $this->statement->setFetchMode(PDO::FETCH_BOTH);
        return $this;
    }

    /**
     * @return $this
     */
    public function fetchKeyPair(): static
    {
        $this->statement->setFetchMode(PDO::FETCH_KEY_PAIR);
        return $this;
    }

    /**
     * @param string $class
     * @param array $params
     * @return $this
     */
    public function fetchClass(string $class, array $params = []): static
    {
        $this->statement->setFetchMode(PDO::FETCH_CLASS, $class, $params);
        return $this;
    }

    /**
     * @param callable $func
     * @return $this
     */
    public function fetchCustom(callable $func): static
    {
        $func($this->statement);
        return $this;
    }
}
