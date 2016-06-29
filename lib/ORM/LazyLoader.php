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

class LazyLoader
{
    use LoaderTrait;

    const FKEY = 1;
    const PKEY = 2;
    const QUERY = 3;
    const PARAMS = 4;
    const WITH = 5;
    const HAS_MANY = 6;
    const IMMEDIATE = 7;
    const READONLY = 8;
    const MODEL = 9;

    /** @var    Connection */
    protected $connection;

    /** @var    Model */
    protected $model;

    /** @var  array */
    protected $options;

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

    /** @var    array */
    protected $params;

    /** @var    bool */
    protected $immediate;

    /**
     * LazyLoader constructor.
     * @param Connection $connection
     * @param array $options
     */
    public function __construct(Connection $connection, array $options)
    {
        $this->connection = $connection;
        $this->model = $options[self::MODEL];
        $this->with = $options[self::WITH];
        $this->hasMany = $options[self::HAS_MANY];
        $this->fk = $options[self::FKEY];
        $this->pk = $options[self::PKEY];
        $this->readonly = $options[self::READONLY];
        $this->query = $options[self::QUERY];
        $this->params = $options[self::PARAMS];
        $this->immediate = $options[self::IMMEDIATE];

        if ($this->immediate) {
            $this->getResults();
        }
    }

    /**
     * @return  array
     */
    protected function getResults()
    {
        if ($this->results === null) {

            $results = $this->connection
                            ->query((string) $this->query, $this->params)
                            ->fetchClass(get_class($this->model), array($this->connection, $this->readonly))
                            ->all();

            $this->prepareResults($this->model, $results);
            $this->results = $results;
        }

        return $this->results;
    }

    /**
     * @param   Model    $model
     * @param   string  $with
     *
     * @return  Model
     */
    protected function getFirst(Model $model, $with)
    {
        $results = $this->getResults();

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
