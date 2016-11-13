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

namespace Opis\Database\ORM\Relation;

use Opis\Database\EntityManager;
use Opis\Database\ORM\DataMapper;
use Opis\Database\ORM\EntityMapper;
use Opis\Database\ORM\EntityQuery;
use Opis\Database\ORM\Query;
use Opis\Database\ORM\Relation;
use Opis\Database\SQL\Join;
use Opis\Database\SQL\SQLStatement;

class HasOneOrManyThrough extends Relation
{
    /** @var  string|null */
    protected $juctionKey;

    /** @var  string|null */
    protected $junctionTable;

    /** @var  string|null */
    protected $joinTable;

    /** @var  string|null */
    protected $joinColumn;

    /** @var  bool */
    protected $hasMany;

    public function __construct(string $entityClass,
                                string $foreignKey = null,
                                string $junctionTable = null,
                                string $junctionKey = null,
                                bool $hasMany = false)
    {
        parent::__construct($entityClass, $foreignKey);
        $this->hasMany = $hasMany;
        $this->juctionKey = $junctionKey;
        $this->junctionTable = $junctionTable;
    }

    /**
     * @param DataMapper $data
     * @param callable|null $callback
     * @return mixed
     */
    protected function getResult(DataMapper $data, callable $callback = null)
    {
        $manager = $data->getEntityManager();
        $owner = $data->getEntityMapper();
        $related = $manager->resolveEntityMapper($this->entityClass);

        if($this->junctionTable === null){
            $table = [$owner->getTable(), $related->getTable()];
            sort($table);
            $this->junctionTable = implode('_', $table);
        }

        if($this->juctionKey === null){
            $this->juctionKey = $related->getForeignKey();
        }

        if($this->foreignKey === null){
            $this->foreignKey = $owner->getForeignKey();
        }

        if($this->joinTable === null){
            $this->joinTable = $related->getTable();
        }

        if($this->joinColumn === null){
            $this->joinColumn = $related->getPrimaryKey();
        }

        $statement = new SQLStatement();

        $select = new class($manager, $related, $statement, $this->junctionTable) extends EntityQuery{

            protected $junctionTable;

            public function __construct(EntityManager $entityManager, EntityMapper $entityMapper, $statement, $table)
            {
                parent::__construct($entityManager, $entityMapper, $statement);
                $this->junctionTable = $table;
            }

            protected function buildQuery()
            {
                $this->locked = true;
                $this->sql->addTables([$this->junctionTable]);
                return $this;
            }
        };

        $select->join($this->joinTable, function (Join $join){
            $join->on($this->junctionTable . '.' . $this->juctionKey, $this->joinTable . '.' . $this->joinColumn);
        })
        ->where($this->junctionTable . '.' . $this->foreignKey)->is($data->getColumn($owner->getPrimaryKey()));

        $statement->addColumn($this->joinTable . '.*');

        if($this->queryCallback !== null || $callback !== null){
            $query = new Query($statement);
            if($this->queryCallback !== null){
                ($this->queryCallback)($query);
            }
            if($callback !== null){
                $callback($query);
            }
        }

        return $this->hasMany ? $select->all() : $select->get();
    }

}