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

use Opis\Database\ORM\DataMapper;
use Opis\Database\ORM\EntityRelation;
use Opis\Database\ORM\RelationQuery;
use Opis\Database\SQL\SelectStatement;
use Opis\Database\SQL\SQLStatement;

class EntityHasOneOrMany extends EntityRelation
{
    protected $hasMany;

    public function __construct(string $entityClass, string $foreignKey = null, bool $hasMany = false)
    {
        parent::__construct($entityClass, $foreignKey);
        $this->hasMany = $hasMany;
    }

    /**
     * @param DataMapper $data
     * @param callable|null $callback
     * @return mixed
     */
    public function getResult(DataMapper $data, callable $callback = null)
    {
        $manager = $data->getEntityManager();
        $owner = $data->getEntityMapper();
        $related = $manager->resolveEntityMapper($this->entityClass);

        $statement = new SQLStatement();
        $select = new SelectStatement($related->getTable(), $statement);

        if($this->queryCallback !== null || $callback !== null){
            $query = new RelationQuery($statement);
            if($this->queryCallback !== null){
                ($this->queryCallback)($query);
            }
            if($callback !== null){
                $callback($query);
            }
        }

        $foreignKey = $this->foreignKey ?? $owner->getForeignKey();

        $select->where($foreignKey)->is($data->getColumn($owner->getPrimaryKey()));

        $class = $related->getClass();
        $connection = $manager->getConnection();
        $compiler = $connection->getCompiler();

        $set = $connection->query($compiler->select($statement), $compiler->getParams())
                          ->fetchAssoc();

        if(!$this->hasMany){
            $result = $set->first();
            if($result === false){
                return null;
            }
            return new $class($manager, $related, $result, false, false);
        }

        $entities = [];

        foreach ($set->all() as $result){
            $entities[] = new $class($manager, $related, $result, false, false);
        }

        return $entities;
    }
}