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

class LazyLoader extends BaseLoader
{
    /** @var    Connection */
    protected $connection;

    /** @var    Model */
    protected $model;

    /** @var    string */
    protected $fk;

    /** @var    string */
    protected $pk;

    /** @var    array */
    protected $results;

    /** @var    bool */
    protected $hasMany;

    /** @var    string */
    protected $query;

    /** @var    bool */
    protected $readonly;

    /** @var    array */
    protected $with;

    /** @var    string */
    protected $modelClass;

    /** @var    array */
    protected $params;

    /** @var    bool */
    protected $immediate;

    /**
     * Constructor
     *
     * @param   Connection  $connection
     * @param   string      $query
     * @param   array       $params
     * @param   array       $with
     * @param   bool        $immediate
     * @param   bool        $readonly
     * @param   bool        $hasMany
     * @param   Model       $model
     * @param   string      $fk
     * @param   string      $pk
     */
    public function __construct(Connection $connection, $query, array $params, array $with, $immediate, $readonly, $hasMany, $model, $fk, $pk)
    {
        $this->connection = $connection;
        $this->modelClass = $model;
        $this->with = $with;
        $this->hasMany = $hasMany;
        $this->fk = $fk;
        $this->pk = $pk;
        $this->readonly = $readonly;
        $this->query = $query;
        $this->params = $params;
        $this->immediate = $immediate;

        if ($immediate) {
            $this->getResults();
        }
    }

    /**
     * @return  array
     */
    protected function &getResults()
    {
        if ($this->results === null) {
            $model = $this->modelClass;
            $this->model = new $model;

            $results = $this->connection
                ->query((string) $this->query, $this->params)
                ->fetchClass($this->modelClass, array($this->readonly, $this->connection))
                ->all();

            $this->prepareResults($this->model, $results);
            $this->results = &$results;
        }

        return $this->results;
    }

    /**
     * @param   Mode    $model
     * @param   string  $with
     *
     * @return  Model
     */
    protected function getFirst(Model $model, $with)
    {
        $results = &$this->getResults();

        foreach ($results as $result) {
            if ($result->{$this->fk} == $model->{$this->pk}) {
                return $result;
            }
        }

        return $model->{$with}()->getResult();
    }

    /**
     * @param   Model   $model
     * @param   string  $with
     *
     * @return  array
     */
    protected function getAll(Model $model, $with)
    {
        $results = &$this->getResults();

        $all = array();

        foreach ($results as $result) {
            if ($result->{$this->fk} == $model->{$this->pk}) {
                $all[] = $result;
            }
        }

        return $all;
    }

    /**
     * @param   Model   $model
     * @param   string  $with
     *
     * @return  array|Model
     */
    public function getResult(Model $model, $with)
    {
        if ($this->hasMany) {
            return $this->getAll($model, $with);
        }

        return $this->getFirst($model, $with);
    }
}
