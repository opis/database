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

namespace Opis\Database;

use Opis\Database\ORM\DataMapper;
use Opis\Database\ORM\EntityMapper;
use Opis\Database\ORM\EntityMapperInterface;
use Opis\Database\ORM\EntityQuery;
use Opis\Database\ORM\Helper\DataMapperHelper;
use Opis\Database\ORM\Helper\EntityHelper;
use Opis\Database\SQL\Compiler;
use Opis\Database\SQL\UpdateStatement;
use RuntimeException;

class EntityManager
{
    /** @var Connection  */
    protected $connection;

    /** @var  Compiler */
    protected $compiler;

    /** @var  string */
    protected $dateFormat;

    /** @var EntityMapper[] */
    protected $entityMappers = [];

    /** @var callable[] */
    protected $entityMappersCallbacks = [];

    /** @var  Database */
    protected $database;

    /**
     * EntityManager constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @return Compiler
     */
    public function getCompiler(): Compiler
    {
        if($this->compiler === null){
            $this->compiler = $this->connection->getCompiler();
        }

        return $this->compiler;
    }

    /**
     * @return string
     */
    public function getDateFormat(): string
    {
        if($this->dateFormat === null){
            $this->dateFormat = $this->getCompiler()->getDateFormat();
        }

        return $this->dateFormat;
    }

    /**
     * @param string $entityClass
     * @return EntityQuery
     */
    public function query(string $entityClass): EntityQuery
    {
        return new EntityQuery($this, $this->resolveEntityMapper($entityClass));
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function save(Entity $entity): bool
    {
        $data = EntityHelper::getDataMapper($entity);

        if($data->isNew()) {

            $id = $this->db()->transaction(function(Database $db) use($data){

                $columns = $data->getRawColumns();

                foreach ($columns as &$column){
                    if($column instanceof Entity){
                        $column = EntityHelper::getPrimaryKey($column);
                    }
                }

                $mapper = $data->getEntityMapper();

                $db->insert($columns)->into($mapper->getTable());

                return $this->connection->getPDO()->lastInsertId($mapper->getSequence());
            })
            ->onError(function(\PDOException $e, Transaction $transaction){
                throw $e;
            })
            ->execute();

            return DataMapperHelper::markAsSaved($data, $id);
        }

        $modified = $data->getModifiedColumns(false);

        if(!empty($modified)){
            $columns = array_intersect_key($data->getRawColumns(), $modified);

            foreach ($columns as &$column){
                if($column instanceof Entity){
                    $column = EntityHelper::getPrimaryKey($column);
                }
            }

            $mapper = $data->getEntityMapper();
            $pk = $mapper->getPrimaryKey();
            $pkv = $data->getColumn($pk);

            DataMapperHelper::markAsUpdated($data);

            return (bool) $this->db()->update($mapper->getTable())
                                         ->where($pk)->is($pkv)
                                         ->set($columns);

        }

        return true;
    }

    /**
     * @param string $class
     * @param array $columns
     * @return Entity
     */
    public function create(string $class, array $columns = []): Entity
    {
        return new $class($this, $this->resolveEntityMapper($class), $columns, false, true);
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function delete(Entity $entity): bool
    {
        return false;
    }

    /**
     * @param string $class
     * @return EntityMapper
     */
    public function resolveEntityMapper(string $class): EntityMapper
    {
        if(isset($this->entityMappers[$class])){
            return $this->entityMappers[$class];
        }

        $reflection = new \ReflectionClass($class);

        if(!$reflection->isSubclassOf(Entity::class)){
            throw new RuntimeException("The '$class' must extend " . Entity::class);
        }

        if(isset($this->entityMappersCallbacks[$class])){
           $callback = $this->entityMappersCallbacks[$class];
        } elseif ($reflection->implementsInterface(EntityMapperInterface::class)){
            $callback = $class . '::mapEntity';
        } else {
            $callback = null;
        }

        $entityMapper = new EntityMapper($class);

        if($callback !== null){
            $callback($entityMapper);
        }

        return $this->entityMappers[$class] = $entityMapper;
    }

    /**
     * @param string $class
     * @param callable $callback
     * @return EntityManager
     */
    public function registerEntityMapper(string $class, callable $callback): self
    {
        $this->entityMappersCallbacks[$class] = $callback;
        return $this;
    }

    /**
     * @return Database
     */
    public function db(): Database
    {
        if($this->database === null){
            $this->database = new Database($this->connection);
        }
        return $this->database;
    }

}