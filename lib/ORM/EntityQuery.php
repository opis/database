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
use Opis\Database\SQL\Delete;
use Opis\Database\SQL\SQLStatement;
use Opis\Database\SQL\Update;

class EntityQuery extends Query
{
    use AggregateTrait;

    /** @var EntityManager */
    protected $manager;

    /** @var EntityMapper */
    protected $mapper;

    /** @var bool */
    protected $locked = false;

    /**
     * EntityQuery constructor.
     * @param EntityManager $entityManager
     * @param EntityMapper $entityMapper
     * @param SQLStatement|null $statement
     */
    public function __construct(EntityManager $entityManager, EntityMapper $entityMapper, SQLStatement $statement = null)
    {
        parent::__construct($statement);
        $this->mapper = $entityMapper;
        $this->manager = $entityManager;
    }

    /**
     * @param string|string[] $names
     * @return EntityQuery
     */
    public function filter($names): self
    {
        if(!is_array($names)){
            $names = [$names];
        }

        $query = new Query($this->sql);
        $filters = $this->mapper->getFilters();

        foreach ($names as $name){
            if(isset($filters[$name])){
                $filters[$name]($query);
            }
        }

        return $this;
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

        return new $class($this->manager, $this->mapper, $result, [], $this->isReadOnly(), false);
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
        $loaders = $this->getLazyLoaders($results);

        foreach ($results as $result){
            $entities[] = new $class($this->manager, $this->mapper, $result, $loaders, $isReadOnly, false);
        }

        return $entities;
    }

    /**
     * @param array $tables
     * @param bool $force
     * @return int
     */
    public function delete(array $tables = [], bool $force = false)
    {
        return $this->transaction(function (Connection $connection) use($tables, $force) {
            if(!$force && $this->mapper->supportsSoftDelete()){
                return (new Update($connection, $this->mapper->getTable(), $this->sql))->set([
                    'deleted_at' => date($this->manager->getDateFormat())
                ]);
            }
            return (new Delete($connection, $this->mapper->getTable(), $this->sql))->delete($tables);
        });
    }

    /**
     * @param array $columns
     * @return int
     */
    public function update(array $columns = [])
    {
        return $this->transaction(function (Connection $connection) use($columns) {
            if($this->mapper->supportsTimestamp()){
                $columns['updated_at'] = date($this->manager->getDateFormat());
            }
            return (new Update($connection, $this->mapper->getTable(), $this->sql))->set($columns);
        });
    }

    /**
     * @param string[]|string $column
     * @param int $value
     * @return int
     */
    public function increment($column, $value = 1)
    {
        return $this->transaction(function (Connection $connection) use($column, $value) {
            if($this->mapper->supportsTimestamp()){
                $this->sql->addUpdateColumns([
                    'updated_at' => date($this->manager->getDateFormat())
                ]);
            }
            return (new Update($connection, $this->mapper->getTable(), $this->sql))->increment($column, $value);
        });
    }

    /**
     * @param string[]|string $column
     * @param int $value
     * @return int
     */
    public function decrement($column, $value = 1)
    {
        return $this->transaction(function(Connection $connection) use($column, $value) {
            if($this->mapper->supportsTimestamp()){
                $this->sql->addUpdateColumns([
                    'updated_at' => date($this->manager->getDateFormat())
                ]);
            }
            return (new Update($connection, $this->mapper->getTable(), $this->sql))->decrement($column, $value);
        });
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
     * @param \Closure $callback
     * @param int $default
     * @return int
     */
    protected function transaction(\Closure $callback, $default = 0)
    {
        $connection = $this->manager->getConnection();
        $pdo = $connection->getPDO();

        if($pdo->inTransaction()){
            return $callback($connection);
        }

        try{
            $pdo->beginTransaction();
            $result = $callback($connection);
            $pdo->commit();
        }catch (\PDOException $exception){
            $pdo->rollBack();
            $result = $default;
        }
        return $result;
    }

    /**
     * @return EntityQuery
     */
    protected function buildQuery()
    {
        $this->sql->addTables([$this->mapper->getTable()]);
        return $this;
    }

    /**
     * @param array $columns
     * @return \Opis\Database\ResultSet;
     */
    protected function query(array $columns)
    {
        if (!$this->buildQuery()->locked && !empty($columns)) {
            $columns[] = $this->mapper->getPrimaryKey();
        }

        if($this->mapper->supportsSoftDelete()){
            if(!$this->withSoftDeleted){
                $this->where('deleted_at')->isNull();
            } elseif ($this->onlySoftDeleted){
                $this->where('deleted_at')->notNull();
            }
        }

        $this->select($columns);

        $connection = $this->manager->getConnection();
        $compiler = $connection->getCompiler();

        return $connection->query($compiler->select($this->sql), $compiler->getParams());
    }
    
    /**
     * @return mixed
     */
    protected function executeAggregate()
    {
        $this->sql->addTables([$this->mapper->getTable()]);

        if($this->mapper->supportsSoftDelete()){
            if(!$this->withSoftDeleted){
                $this->where('deleted_at')->isNull();
            } elseif ($this->onlySoftDeleted){
                $this->where('deleted_at')->notNull();
            }
        }

        $connection = $this->manager->getConnection();
        $compiler = $connection->getCompiler();

        return $connection->column($compiler->select($this->sql), $compiler->getParams());
    }


    /**
     * @return bool
     */
    protected function isReadOnly(): bool
    {
        return !empty($this->sql->getJoins());
    }

    /**
     * @param array $results
     * @return array
     */
    protected function getLazyLoaders(array $results): array
    {
        if(empty($this->with) || empty($results)){
            return [];
        }

        $loaders = [];
        $attr = $this->getWithAttributes();
        $relations = $this->mapper->getRelations();

        $lazyLoader = function (EntityManager $manager, EntityMapper $owner, array $options){
            return $this->getLazyLoader($manager, $owner, $options);
        };

        foreach ($attr['with'] as $with => $callback) {
            if(!isset($relations[$with])){
                continue;
            }
            $loader = $lazyLoader->call($relations[$with], $this->manager, $this->mapper,[
                'results' => $results,
                'callback' => $callback,
                'with' => $attr[$with]['extra'] ?? [],
                'immediate' => $this->immediate,
            ]);

            if(null === $loader){
                continue;
            }
            $loaders[$with] = $loader;
        }

        return $loaders;
    }
}