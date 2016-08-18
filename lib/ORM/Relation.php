<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2015 Marius Sarca
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

use Opis\Database\Model;
use Opis\Database\Connection;
use Opis\Database\SQL\BaseStatement;
use Opis\Database\SQL\Delete;
use Opis\Database\SQL\HavingStatement;
use Opis\Database\SQL\SelectStatement;
use Opis\Database\SQL\Update;
use RuntimeException;

abstract class Relation extends BaseStatement
{
    use LoaderTrait;
    use SelectTrait{
        select as protected;
    }

    /** @var    Model */
    protected $model;

    /** @var    string */
    protected $foreignKey;

    /** @var    Connection */
    protected $connection;

    /** @var    Model */
    protected $owner;

    /** @var  HavingStatement */
    protected $have;

    /** @var  bool */
    protected $locked =false;

    /** @var bool  */
    protected $includeSoftDeletes = false;

    /** @var bool  */
    protected $onlySoftDeletes = false;


    /**
     * Constructor
     *
     * @param   Model       $owner
     * @param   Model       $model
     * @param   string|null $foreignKey (optional)
     */
    public function __construct(Model $owner, Model $model, $foreignKey = null)
    {
        $this->connection = $owner->getConnection();
        $this->model = $model;
        $this->foreignKey = $foreignKey;
        $this->owner = $owner;
        parent::__construct();
        $this->have = new HavingStatement($this->sql);
    }


    /**
     * @return  Model|Model[]|false
     */
    abstract public function getResult();

    /**
     * @param   Model   $model
     * @param   string  $name
     *
     * @return  string
     */
    public function getRelatedColumn(Model $model, $name): string
    {
        return $name;
    }

    /**
     * @param   array   $options
     *
     * @return  LazyLoader
     */
    public function getLazyLoader(array $options): LazyLoader
    {
        $fk = $this->getForeignKey();
        $pk = $this->owner->getPrimaryKey();

        $ids = $options['ids'];
        $with = $options['with'];
        $callback = $options['callback'];
        $immediate = $options['immediate'];

        $select = new SelectStatement($this->model->getTable());
        $select->where($fk)->in($ids);

        if ($callback !== null) {
            $callback($select);
        }
        
        $compiler = $this->connection->getCompiler();

        $options = [
            LazyLoader::QUERY => $compiler->select($select->getSQLStatement()),
            LazyLoader::PARAMS => $compiler->getParams(),
            LazyLoader::WITH => $with,
            LazyLoader::READONLY => null,
            LazyLoader::HAS_MANY => $this->hasMany(),
            LazyLoader::MODEL => $this->model,
            LazyLoader::FKEY => $fk,
            LazyLoader::PKEY => $pk,
            LazyLoader::IMMEDIATE => $immediate,
        ];

        return new LazyLoader($this->connection, $options);
    }

    /**
     * @return  string
     */
    public function getForeignKey(): string 
    {
        if ($this->foreignKey === null) {
            $this->foreignKey = $this->owner->getForeignKey();
        }

        return $this->foreignKey;
    }

    /**
     * @return  Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @return  Model
     */
    public function getOwner(): Model
    {
        return $this->owner;
    }

    /**
     * @param array $columns
     * @return Model|false
     */
    public function first(array $columns = [])
    {
        return $this->query($columns)
            ->fetchClass(get_class($this->model), [$this->connection])
            ->first();
    }

    /**
     * @param array $columns
     * @return Model[]
     */
    public function all(array $columns = []): array
    {
        $results = $this->query($columns)
            ->fetchClass(get_class($this->model), [$this->connection])
            ->all();

        $this->prepareResults($this->model, $results);

        return $results;
    }

    /**
     * @return self
     */
    public function withSoftDeleted(): self
    {
        $this->includeSoftDeletes = true;
        return $this;
    }

    /**
     * @return self
     */
    public function onlySoftDeleted(): self
    {
        $this->onlySoftDeletes = $this->includeSoftDeletes = true;
        return $this;
    }

    /**
     * @param array $tables
     * @return int
     */
    public function delete(array $tables = [])
    {
        return (new Delete($this->connection, $this->model->getTable(), $this->buildQuery()->sql))->delete($tables);
    }

    /**
     * @return int
     * @throws RuntimeException
     */
    public function softDelete()
    {
        if (!$this->model->supportsSoftDeletes()) {
            throw new RuntimeException('Soft deletes is not supported for this model');
        }

        return (new Update($this->connection, $this->model->getTable(), $this->buildQuery()->sql))->set([
            'deleted_at' => date($this->model->getDateFormat()),
        ]);
    }

    /**
     * @return int
     * @throws RuntimeException
     */
    public function restore()
    {
        if (!$this->model->supportsSoftDeletes()) {
            throw new RuntimeException('Soft deletes is not supported for this model');
        }

        return (new Update($this->connection, $this->model->getTable(), $this->buildQuery()->sql))->set([
            'deleted_at' => null,
        ]);
    }

    /**
     * @param array $columns
     * @return int
     */
    public function update(array $columns = [])
    {
        if($this->model->supportsTimestamps()){
            $columns['updated_at'] = date($this->model->getDateFormat());
        }

        return (new Update($this->connection, $this->model->getTable(), $this->buildQuery()->sql))->set($columns);
    }

    /**
     * @return Relation
     */
    protected function lock(): self
    {
        $this->locked = true;
        return $this;
    }

    /**
     * @return  bool
     */
    protected function hasMany(): bool
    {
        return true;
    }

    /**
     * Build query
     * 
     * @return  self
     */
    protected function buildQuery(): self
    {
        $this->sql->addTables([$this->model->getTable()]);
        return $this->where($this->getForeignKey())->is($this->owner->{$this->owner->getPrimaryKey()});
    }

    /**
     * @param   array   &$columns   (optional)
     *
     * @return  \Opis\Database\ResultSet
     */
    protected function query(array &$columns = array())
    {
        $pk = $this->model->getPrimaryKey();

        if (!$this->buildQuery()->locked && !empty($columns)) {
            $columns[] = $pk;
        }

        if($this->model->supportsSoftDeletes()){
            if (!$this->includeSoftDeletes) {
                $this->where('deleted_at')->isNull();
            } elseif ($this->onlySoftDeletes) {
                $this->where('deleted_at')->notNull();
            }
        }

        $this->select($columns);
        $compiler = $this->connection->getCompiler();

        return $this->connection->query($compiler->select($this->sql), $compiler->getParams());
    }

    /**
     * @return HavingStatement
     */
    protected function getHavingStatement(): HavingStatement
    {
        return $this->have;
    }
    /**
     * @return mixed
     */
    protected function executeStatement()
    {
        if($this->model->supportsSoftDeletes()){
            if (!$this->includeSoftDeletes) {
                $this->where('deleted_at')->isNull();
            } elseif ($this->onlySoftDeletes) {
                $this->where('deleted_at')->notNull();
            }
        }
        $this->sql->addTables([$this->model->getTable()]);
        $compiler = $this->connection->getCompiler();
        return $this->connection->column($compiler->select($this->sql), $compiler->getParams());
    }

}
