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
use Opis\Database\Entity;
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

    /** @var EntityManager */
    protected $manager;

    /** @var EntityMapper */
    protected $mapper;

    /**
     * EntityQuery constructor.
     * @param EntityManager $entityManager
     * @param EntityMapper $entityMapper
     * @param SQLStatement|null $statement
     */
    public function __construct(EntityManager $entityManager, EntityMapper $entityMapper, SQLStatement $statement = null)
    {
        parent::__construct($statement);
        $this->have = new HavingStatement($this->sql);
        $this->mapper = $entityMapper;
        $this->manager = $entityManager;
    }

    /**
     * @param array $columns
     * @return null|Entity
     */
    public function get(array $columns = [])
    {
        $result = $this->query($columns)
                       ->fetchAssoc()
                       ->first();

        if($result === false){
            return null;
        }

        $class = $this->mapper->getClass();

        return new $class($this->manager, $this->mapper, $result, $this->isReadOnly(), false);
    }

    /**
     * @param array $columns
     * @return Entity[]
     */
    public function all(array $columns = []): array
    {
        $results = $this->query($columns)
                         ->fetchAssoc()
                         ->all();

        $entities = [];

        $class = $this->mapper->getClass();
        $isReadOnly = $this->isReadOnly();

        foreach ($results as $result){
            $entities[] = new $class($this->manager, $this->mapper, $result, $isReadOnly, false);
        }

        return $entities;
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed|null
     */
    public function find($id, array $columns = [])
    {
        return $this->where($this->mapper->getPrimaryKey())->is($id)
                    ->get($columns);
    }

    /**
     * @param array $ids
     * @param array $columns
     * @return array
     */
    public function findAll(array $ids, array $columns = []): array
    {
        return $this->where($this->mapper->getPrimaryKey())->in($ids)
                    ->all($columns);
    }

    /**
     * @param array $columns
     * @return \Opis\Database\ResultSet;
     */
    protected function query(array $columns)
    {
        if (!empty($columns)) {
            $columns[] = $this->mapper->getPrimaryKey();
        }

        $this->sql->addTables([$this->mapper->getTable()]);
        $this->select($columns);

        $connection = $this->manager->getConnection();
        $compiler = $connection->getCompiler();

        return $connection->query($compiler->select($this->sql), $compiler->getParams());
    }

    /**
     * @return HavingStatement
     */
    protected function getHavingStatement(): HavingStatement
    {
        return $this->have;
    }

    /**
     * @return bool
     */
    protected function isReadOnly(): bool
    {
        return empty($this->sql->getJoins());
    }

}