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

use RuntimeException;
use Opis\Database\Model;
use Opis\Database\Connection;

class Query extends BaseQuery
{
    /** @var    Model */
    protected $model;

    /** @var    Connection */
    protected $connection;

    /**
     * Constructor
     *
     * @param   Connection  $connection
     * @param   Model       $model
     */
    public function __construct(Connection $connection, Model $model)
    {
        $this->model = $model;
        $this->connection = $connection;

        $compiler = $connection->compiler();
        $query = new Select($model, $compiler);
        $whereCondition = new WhereCondition($this, $query);

        parent::__construct($compiler, $query, $whereCondition);
    }

    /**
     *  @param  array   &$columns   (optional)
     *
     *  @return \Opis\Database\ResultSet
     */
    protected function query(array &$columns = array())
    {
        $pk = $this->model->getPrimaryKey();

        if (!empty($columns)) {
            $columns[] = $pk;
        }

        return $this->connection->query((string) $this->query->select($columns), $this->query->getCompiler()->getParams());
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
        return $this->query->toDelete($this->connection)->delete($tables);
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

        return $this->query->toUpdate($this->connection)->update(array(
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

        return $this->query->onlySoftDeleted()->toUpdate($this->connection)->update(array(
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
        return $this->query->toUpdate($this->connection)->update($columns);
    }

    /**
     * @param   string  $name
     *
     * @return  mixed
     */
    public function column($name)
    {
        $this->query->column($name);
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
        $this->query->count($column, $distinct);
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
        $this->query->avg($column, $distinct);
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
        $this->query->sum($column, $distinct);
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
        $this->query->min($column, $distinct);
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
        $this->query->max($column, $distinct);
        return $this->execute();
    }

    /**
     * @param   array   $columns    (optional)
     *
     * @return  Model|false
     */
    public function first(array $columns = array())
    {
        return $this->query()
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
     * @param   mixed   $id
     * @param   array   $columns    (optional)
     *
     * @return  Model|false
     */
    public function find($id, array $columns = array())
    {
        $this->query->where($this->model->getPrimaryKey())->is($id);
        return $this->first($columns);
    }

    /**
     * @param   array   $columns    (optional)
     *
     * @return  array
     */
    public function findAll(array $columns = array())
    {
        return $this->findMany(array(), $columns);
    }

    /**
     * @param   array|null  $ids        (optional)
     * @param   array       $columns    (optional)
     *
     * @return  array
     */
    public function findMany(array $ids = null, array $columns = array())
    {
        if ($ids !== null && !empty($ids)) {
            $this->query->where($this->model->getPrimaryKey())->in($ids);
        }
        return $this->all($columns);
    }
}
