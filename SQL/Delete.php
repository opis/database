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

namespace Opis\Database\SQL;

use Opis\Database\Database;

class Delete extends DeleteStatement
{
    protected $database;
    
    public function __construct(Database $database, Compiler $compiler, $from, $joins, Where $where = null)
    {
        parent::__construct($compiler, $from, $where);
        $this->database = $database;
        $this->joins = $joins;
    }
    
    public function delete($tables = array())
    {
        parent::delete($tables);
        die('<pre>'.$this->database->replaceParams((string) $this, $this->compiler->getParams()));
        return $database->count((string) $this, $this->compiler->getParams());
    }
    
}