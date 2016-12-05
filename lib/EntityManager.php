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
use Opis\Database\SQL\Compiler;
use Opis\Database\SQL\Insert;
use Opis\Database\SQL\Update;
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
        /** @var DataMapper $data */
        $data = $this->getDataMapper($entity);

        if($data->isDeleted()){
            throw new RuntimeException("The record is deleted");
        }

        if($data->isNew()) {

            $id = $this->transaction(function (Connection $connection) use($data) {
                $columns = $data->getRawColumns();

                foreach ($columns as &$column){
                    if($column instanceof Entity){
                        $column = $this->getPKValue($column);
                    }
                }

                $mapper = $data->getEntityMapper();

                if(null !== $pkgen = $mapper->getPrimaryKeyGenerator()){
                    $columns[$mapper->getPrimaryKey()] = $pk = $pkgen($data);
                }

                if($mapper->supportsTimestamp()){
                    $columns['created_at'] = date($this->getDateFormat());
                    $columns['updated_at'] = null;
                }

                (new Insert($connection))->insert($columns)->into($mapper->getTable());

                if($pkgen !== null){
                    return $pk ?? false;
                }

                return $connection->getPDO()->lastInsertId($mapper->getSequence());
            }, false);

            return $id !== false ? $this->markAsSaved($data, $id) : false;
        }

        $modified = $data->getModifiedColumns(false);

        if(!empty($modified)){
            return $this->transaction(function (Connection $connection) use($data, $modified) {
                $columns = array_intersect_key($data->getRawColumns(), $modified);

                foreach ($columns as &$column){
                    if($column instanceof Entity){
                        $column = $this->getPKValue($column);
                    }
                }

                $mapper = $data->getEntityMapper();
                $pk = $mapper->getPrimaryKey();
                $pkv = $data->getColumn($pk);

                $updatedAt = null;

                if($mapper->supportsTimestamp()){
                    $columns['updated_at'] = $updatedAt = date($this->getDateFormat());
                }

                $this->markAsUpdated($data, $updatedAt);

                return (bool) (new Update($connection, $mapper->getTable()))
                    ->where($pk)->is($pkv)
                    ->set($columns);
            }, false);
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
        return new $class($this, $this->resolveEntityMapper($class), $columns, [], false, true);
    }

    /**
     * @param Entity $entity
     * @return bool
     */
    public function delete(Entity $entity): bool
    {
        return $this->transaction(function() use($entity) {
            $data = $this->getDataMapper($entity);

            if($data->isDeleted()){
                throw new RuntimeException("The record was already deleted");
            }

            if($data->isNew()){
                throw new RuntimeException("Can't delete an unsaved entity");
            }

            $mapper = $data->getEntityMapper();
            $pk = $mapper->getPrimaryKey();
            $pkv = $data->getColumn($pk);

            $this->markAsDeleted($data);

            return (bool)(new EntityQuery($this, $mapper))->where($pk)->is($pkv)->delete();
        }, false);
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

    protected function transaction(\Closure $callback, $default = false)
    {
        $pdo = $this->connection->getPDO();

        if($pdo->inTransaction()){
            return $callback($this->connection);
        }

        try{
            $pdo->beginTransaction();
            $result = $callback($this->connection);
            $pdo->commit();
        }catch (\PDOException $exception){
            $pdo->rollBack();
            $result = $default;
        }

        return $result;
    }

    /**
     * @param Entity $entity
     * @return DataMapper
     */
    protected function getDataMapper(Entity $entity): DataMapper
    {
        static $closure;
        if($closure === null){
            $closure = function (){
                return $this->orm();
            };
        }
        return $closure->call($entity);
    }

    /**
     * @param Entity $entity
     * @return mixed
     */
    protected function getPKValue(Entity $entity)
    {
        static $closure;
        if($closure === null){
            $closure = function (){
                /** @var DataMapper $data */
                $data = $this->orm();
                return $data->getColumn($data->getEntityMapper()->getPrimaryKey());
            };
        }
        return $closure->call($entity);
    }

    /**
     * @param DataMapper $data
     * @param $id
     * @return bool
     */
    protected function markAsSaved(DataMapper $data, $id): bool
    {
        static $closure;
        if($closure === null){
            $closure = function ($id){
                $this->rawColumns[$this->mapper->getPrimaryKey()] = $id;
                $this->dehidrated = true;
                $this->isNew = false;
                $this->modified = [];
                return true;
            };
        }
        return $closure->call($data, $id);
    }

    /**
     * @param DataMapper $data
     * @return mixed
     */
    protected function markAsDeleted(DataMapper $data)
    {
        static $closure;
        if($closure === null){
            $closure = function (){
                $this->deleted = true;
            };
        }
        return $closure->call($data);
    }

    /**
     * @param DataMapper $data
     * @param string|null $updatedAt
     * @return mixed
     */
    protected function markAsUpdated(DataMapper $data, string $updatedAt = null)
    {
        static $closure;
        if($closure === null){
            $closure = function (string $updatedAt = null){
                if($updatedAt !== null){
                    unset($this->columns['updated_at']);
                    $this->rawColumns['updated_at'] = $updatedAt;
                }
                $this->modified = [];
            };
        }
        return $closure->call($data, $updatedAt);
    }
}