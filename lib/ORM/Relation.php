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

abstract class Relation extends BaseQuery
{
    /** @var    Model */
    protected $model;

    /** @var    string */
    protected $foreignKey;

    /** @var    Connection */
    protected $connection;

    /** @var    Model */
    protected $owner;

    /**
     * Constructor
     *
     * @param   Connection  $connection
     * @param   Model       $owner
     * @param   Model       $model
     * @param   string|null $foreignKey (optional)
     */
    public function __construct(Connection $connection, Model $owner, Model $model, $foreignKey = null)
    {
        $this->connection = $connection;
        $this->model = $model;
        $this->foreignKey = $foreignKey;
        $this->owner = $owner;

        $compiler = $connection->compiler();
        $query = new Select($this->model, $compiler);
        $whereCondition = new WhereCondition($this, $query);

        parent::__construct($compiler, $query, $whereCondition);
    }

    /**
     * @return  bool
     */
    protected function hasMany()
    {
        return true;
    }

    /**
     * @param   Model   $model
     * @param   string  $name
     *
     * @return  string
     */
    public function getRelatedColumn(Model $model, $name)
    {
        return $name;
    }

    /**
     * @param   array   $options
     *
     * @return  LazyLoader
     */
    public function getLazyLoader(array $options)
    {
        $fk = $this->getForeignKey();
        $pk = $this->owner->getPrimaryKey();

        $ids = $options['ids'];
        $with = $options['with'];
        $callback = $options['callback'];
        $immediate = $options['immediate'];

        $select = new Select($this->model, $this->compiler);

        $select->where($fk)->in($ids);

        if ($callback !== null) {
            $callback($select);
        }

        $query = (string) $select;
        $params = $select->getCompiler()->getParams();

        return new LazyLoader($this->connection, $query, $params, $with, $immediate, $this->isReadOnly, $this->hasMany(), get_class($this->model), $fk, $pk);
    }

    /**
     * @return  string
     */
    public function getForeignKey()
    {
        if ($this->foreignKey === null) {
            $this->foreignKey = $this->owner->getForeignKey();
        }

        return $this->foreignKey;
    }

    /**
     * @param   array   &$columns   (optional)
     *
     * @return  \Opis\Database\ResultSet
     */
    protected function query(array &$columns = array())
    {
        $pk = $this->model->getPrimaryKey();

        $query = $this->buildQuery();

        if (!$query->isLocked() && !empty($columns)) {
            $columns[] = $pk;
        }

        return $this->connection->query((string) $query->select($columns), $query->getCompiler()->getParams());
    }

    /**
     * @return  mixed
     */
    protected function execute()
    {
        return $this->connection->column((string) $this->query, $this->compiler->getParams());
    }

    /**
     * @param   array   $tables (optional)
     *
     * @return  int
     */
    public function delete(array $tables = array())
    {
        return $this->buildQuery()->toDelete($this->connection)->delete($tables);
    }

    /**
     * @return  boolean
     * 
     * @throws  RuntimeException
     */
    public function softDelete()
    {
        if (!$this->query->supportsSoftDeletes()) {
            throw new RuntimeException('Soft deletes is not supported for this model');
        }

        return $this->buildQuery()->toUpdate($this->connection)->update(array(
            'deleted_at' => date($this->model->getDateFormat()),
        ), true);
    }

    /**
     * @return  boolean
     * 
     * @throws  RuntimeException
     */
    public function restore()
    {
        if (!$this->query->supportsSoftDeletes()) {
            throw new RuntimeException('Soft deletes is not supported for this model');
        }

        return $this->buildQuery()->onlySoftDeleted()->toUpdate($this->connection)->update(array(
            'deleted_at' => null,
        ), true);
    }

    /**
     * @param   array   $columns
     *
     * @return  int
     */
    public function update(array $columns)
    {
        return $this->buildQuery()->toUpdate($this->connection)->update($columns);
    }

    /**
     * @param   string  $name
     *
     * @return  mixed
     */
    public function column($name)
    {
        $this->buildQuery()->column($name);
        return $this->execute();
    }

    /**
     * @param   string  $column     (optional)
     * @param   bool    $distinct   (optional)
     *
     * @return  mixed
     */
    public function count($column = '*', $distinct = false)
    {
        $this->buildQuery()->count($column, $distinct);
        return $this->execute();
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     *
     * @return  mixed
     */
    public function avg($column, $distinct = false)
    {
        $this->buildQuery()->avg($column, $distinct);
        return $this->execute();
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     *
     * @return  mixed
     */
    public function sum($column, $distinct = false)
    {
        $this->buildQuery()->sum($column, $distinct);
        return $this->execute();
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     *
     * @return  mixed
     */
    public function min($column, $distinct = false)
    {
        $this->buildQuery()->min($column, $distinct);
        return $this->execute();
    }

    /**
     * @param   string  $column
     * @param   bool    $distinct   (optional)
     *
     * @return  mixed
     */
    public function max($column, $distinct = false)
    {
        $this->buildQuery()->max($column, $distinct);
        return $this->execute();
    }

    /**
     * @param   array   $columns    (optional)
     *
     * @return  Model|false
     */
    public function first(array $columns = array())
    {
        return $this->query($columns)
                ->fetchClass(get_class($this->model), array($this->isReadOnly, $this->connection))
                ->first();
    }

    /**
     * @param   array   $columns    (optional)
     *
     * @return  array
     */
    public function all(array $columns = array())
    {
        $results = $this->query($columns)
            ->fetchClass(get_class($this->model), array($this->isReadOnly, $this->connection))
            ->all();

        $this->prepareResults($this->model, $results);

        return $results;
    }

    /**
     * @return  Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return  Model
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Build query
     * 
     * @return  Select
     */
    protected function buildQuery()
    {
        return $this->query->where($this->getForeignKey())->is($this->owner->{$this->owner->getPrimaryKey()});
    }

    /**
     * @return  Model
     */
    abstract public function getResult();
}
