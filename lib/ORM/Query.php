<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2016 Marius Sarca
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
use Opis\Database\SQL\BaseStatement;
use Opis\Database\SQL\Delete;
use Opis\Database\SQL\HavingStatement;
use Opis\Database\SQL\SQLStatement;
use Opis\Database\SQL\Update;
use Opis\Database\SQL\WhereStatement;
use RuntimeException;

class Query extends BaseStatement
{
    use LoaderTrait;
    use SelectTrait {
        select as protected;
    }

    protected $model;
    protected $have;
    protected $connection;
    protected $includeSoftDeletes = false;
    protected $onlySoftDeletes = false;

    public function __construct(Model $model, SQLStatement $statement = null)
    {
        parent::__construct($statement);
        $this->model = $model;
        $this->connection = $model->getConnection();
        $this->have = new HavingStatement($this->sql);
    }

    /**
     * @param array $columns
     * @return Model|false
     */
    public function first(array $columns = [])
    {
        return $this->query($columns)
                    ->fetchClass(get_class($this->model), [$this->connection, $this->isReadOnly()])
                    ->first();
    }

    /**
     * @param array $columns
     * @return Model[]
     */
    public function all(array $columns = []): array
    {
        $results =  $this->query($columns)
                         ->fetchClass(get_class($this->model), [$this->connection, $this->isReadOnly()])
                         ->all();

        $this->prepareResults($this->model, $results);

        return $results;
    }

    /**
     * @param $id
     * @param array $columns
     * @return Model|false
     */
    public function find($id, array $columns = [])
    {
        return $this->where($this->model->getPrimaryKey())->is($id)->first($columns);
    }

    /**
     * @param array|null $ids
     * @param array $columns
     * @return Model[]
     */
    public function findMany(array $ids = null, array $columns = []): array
    {
        if($ids !== null){
            $this->where($this->model->getPrimaryKey())->in($ids);
        }
        return $this->all($columns);
    }

    /**
     * @param array $columns
     * @return Model[]
     */
    public function findAll(array $columns = []): array
    {
        return $this->findMany(null, $columns);
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
     * @return Query|BaseStatement|WhereStatement
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
        return (new Delete($this->connection, $this->model->getTable(), $this->sql))->delete($tables);
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

        return (new Update($this->connection, $this->model->getTable(), $this->sql))->set([
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

        return (new Update($this->connection, $this->model->getTable(), $this->sql))->set([
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

        return (new Update($this->connection, $this->model->getTable(), $this->sql))->set($columns);
    }

    /**
     * @return bool|null
     */
    protected function isReadOnly()
    {
        return empty($this->sql->getJoins()) ? null : true;
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

    /**
     * @param array $columns
     * @return \Opis\Database\ResultSet
     */
    protected function query($columns = array())
    {
        $pk = $this->model->getPrimaryKey();

        if (!empty($columns)) {
            $columns[] = $pk;
        }

        if($this->model->supportsSoftDeletes()){
            if (!$this->includeSoftDeletes) {
                $this->where('deleted_at')->isNull();
            } elseif ($this->onlySoftDeletes) {
                $this->where('deleted_at')->notNull();
            }
        }

        $this->sql->addTables([$this->model->getTable()]);
        $this->select($columns);
        $compiler = $this->connection->getCompiler();

        return $this->connection->query($compiler->select($this->sql), $compiler->getParams());
    }
}