<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013 Marius Sarca
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
    
    protected $statement;
    
    public function __construct(PDOStatement $statement)
    {
        $this->statement = $statement;
    }
    
    public function __destruct()
    {
        $this->statement->closeCursor();
    }
    
    public function all()
    {
        return $this->statement->fetchAll();
    }
    
    public function first()
    {
        $result = $this->statement->fetch();
        $this->statement->closeCursor();
        return $result;
    }
    
    public function next()
    {
        return $this->statement->fetch();
    }
    
    public function flush()
    {
        return $this->statement->closeCursor();
    }
    
    public function fetchAssoc()
    {
        $this->statement->setFetchMode(PDO::FETCH_ASSOC);
        return $this;
    }
    
    public function fetchObject()
    {
        $this->statement->setFetchMode(PDO::FETCH_OBJ);
        return $this;
    }
    
    public function fetchNamed()
    {
        $this->statement->setFetchMode(PDO::FETCH_NAMED);
        return $this;
    }
    
    public function fetchNum()
    {
        $this->statement->setFetchMode(PDO::FETCH_NUM);
        return $this;
    }
    
    public function fetchBoth()
    {
        $this->statement->setFetchMode(PDO::FETCH_BOTH);
        return $this;
    }
    
}