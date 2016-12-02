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
use Opis\Database\ORM\LazyLoader;
use Opis\Database\ORM\Relation;
use Opis\Database\ORM\Query;
use Opis\Database\SQL\SelectStatement;
use Opis\Database\SQL\SQLStatement;

class HasOneOrMany extends Relation
{
    /** @var bool */
    protected $hasMany;

    /**
     * EntityHasOneOrMany constructor.
     * @param string $entityClass
     * @param string|null $foreignKey
     * @param bool $hasMany
     */
    public function __construct(string $entityClass, string $foreignKey = null, bool $hasMany = false)
    {
        parent::__construct($entityClass, $foreignKey);
        $this->hasMany = $hasMany;
    }

    /**
     * @param DataMapper $owner
     * @param DataMapper $related
     */
    public function addRelatedEntity(DataMapper $owner, DataMapper $related)
    {
        if($this->foreignKey === null){
            $this->foreignKey = $owner->getEntityMapper()->getForeignKey();
        }

        $related->setColumn($this->foreignKey, $owner->getColumn($owner->getEntityMapper()->getPrimaryKey()));
    }

    /**
     * @param EntityManager $manager
     * @param EntityMapper $owner
     * @param array $options
     * @return LazyLoader
     */
    protected function getLazyLoader(EntityManager $manager, EntityMapper $owner, array $options)
    {
        $related = $manager->resolveEntityMapper($this->entityClass);

        if($this->foreignKey === null){
            $this->foreignKey = $owner->getForeignKey();
        }

        $ids = [];
        $pk = $owner->getPrimaryKey();
        foreach ($options['results'] as $result){
            $ids[] = $result[$pk];
        }

        $statement = new SQLStatement();
        $select = new EntityQuery($manager, $related, $statement);

        $select->where($this->foreignKey)->in($ids);

        if($options['callback'] !== null){
            $options['callback'](new Query($statement));
        }

        $select->with($options['with'], $options['immediate']);
        
        return new LazyLoader($select, $pk, $this->foreignKey, $this->hasMany, $options['immediate']);
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

        if($this->foreignKey === null){
            $this->foreignKey = $owner->getForeignKey();
        }

        $statement = new SQLStatement();
        $select = new EntityQuery($manager, $related, $statement);

        $select->where($this->foreignKey)->is($data->getColumn($owner->getPrimaryKey()));

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