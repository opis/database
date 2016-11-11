<?php
/* ===========================================================================
 * Copyright 2013-2016 The Opis Project
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

namespace Opis\Database\ORM;

use Opis\Database\Connection;
use Opis\Database\EntityManager;
use Opis\Database\SQL\BaseStatement;
use Opis\Database\SQL\HavingStatement;
use Opis\Database\SQL\SQLStatement;

class EntityQuery extends BaseStatement
{
    use SelectTrait {
        select as protected;
    }

    /** @var HavingStatement */
    protected $have;
    protected $enityMapper;

    public function __construct(EntityManager $entityManager, EntityMapper $entityMapper)
    {
        $this->have = new HavingStatement($this->sql);
        $this->enityMapper = $entityMapper;
    }

    public function find($id)
    {

    }

    public function findMany()
    {

    }

    public function findAll()
    {
        
    }

    public function first(array $columns = [])
    {
        
    }

    public function all(array $columns = [])
    {
        $results = $this->query($columns)
                         ->fetchAssoc()
                         ->all();
    }

    /**
     * @param array $columns
     * @return \Opis\Database\ResultSet
     */
    protected function query(array $columns)
    {
        if (!empty($columns)) {
            $columns[] = $this->enityMapper->getPrimaryKey();
        }
    }

    protected function getHavingStatement(): HavingStatement
    {
        return $this->have;
    }

    protected function executeStatement()
    {
        // TODO: Implement executeStatement() method.
    }


}