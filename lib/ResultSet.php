<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2015 Marius Sarca
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
use Closure;
use PDOStatement;

class ResultSet
{
    /** @var    \PDOStatement   The PDOStatement associated with this result set. */
    protected $statement;

    /**
     * Constructor
     *
     * @param   \PDOStatement   $statement  The PDOStatement associated with this result set.
     */
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
    public function count()
    {
        return $this->statement->rowCount();
    }

    /**
     * Fetch all results
     *
     * @param   callable    $callable   (optional) Callback function
     * @param   int         $fetchStyle (optional) PDO fetch style
     *
     * @return  array
     */
    public function all($callable = null, $fetchStyle = 0)
    {
        if ($callable === null) {
            return $this->statement->fetchAll($fetchStyle);
        }
        return $this->statement->fetchAll($fetchStyle | PDO::FETCH_FUNC, $callable);
    }

    /**
     * @param   bool        $uniq       (optional)
     * @param   callable    $callable   (optional)
     * 
     * @return  array
     */
    public function allGroup($uniq = false, $callable = null)
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
     * @param   callable    $callable   (optional) Callback function
     *
     * @return  mixed
     */
    public function first($callable = null)
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
     * @return  mixed
     */
    public function next()
    {
        return $this->statement->fetch();
    }

    /**
     * Close current cursor
     *
     * @return  mixed
     */
    public function flush()
    {
        return $this->statement->closeCursor();
    }

    /**
     * Return a column
     *
     * @param   int $col    0-indexed number of the column you wish to retrieve
     * 
     * @return  mixed
     */
    public function column($col = 0)
    {
        return $this->statement->fetchColumn($col);
    }

    /**
     * Fetch each result as an associative array
     *
     * @return  $this
     */
    public function fetchAssoc()
    {
        $this->statement->setFetchMode(PDO::FETCH_ASSOC);
        return $this;
    }

    /**
     * Fetch each result as an stdobject
     *
     * @return  $this
     */
    public function fetchObject()
    {
        $this->statement->setFetchMode(PDO::FETCH_OBJ);
        return $this;
    }

    /**
     * @return  $this
     */
    public function fetchNamed()
    {
        $this->statement->setFetchMode(PDO::FETCH_NAMED);
        return $this;
    }

    /**
     * @return  $this
     */
    public function fetchNum()
    {
        $this->statement->setFetchMode(PDO::FETCH_NUM);
        return $this;
    }

    /**
     * @return  $this
     */
    public function fetchBoth()
    {
        $this->statement->setFetchMode(PDO::FETCH_BOTH);
        return $this;
    }

    /**
     * @return  $this
     */
    public function fetchKeyPair()
    {
        $this->statement->setFetchMode(PDO::FETCH_KEY_PAIR);
        return $this;
    }

    /**
     * @param   string  $class
     * @param   array   $ctorargs   (optional)
     * 
     * @return  $this
     */
    public function fetchClass($class, array $ctorargs = array())
    {
        $this->statement->setFetchMode(PDO::FETCH_CLASS, $class, $ctorargs);
        return $this;
    }

    /**
     * @param   Closure $func
     * 
     * @return  $this
     */
    public function fetchCustom(Closure $func)
    {
        $func($this->statement);
        return $this;
    }
}
