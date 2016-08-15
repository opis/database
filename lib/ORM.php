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

namespace Opis\Database;
use Opis\Database\ORM\Query;

/**
 * Class ORM
 * @package Opis\Database
 *
 * @method  \Opis\Database\ORM::model
 */
class ORM
{
    /** @var  Connection */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param $class
     * @return Model|Query
     */
    public function __invoke(string $class)
    {
        return $this->model($class);
    }

    /**
     * @param string $class
     * @return Model|Query
     */
    public function model(string $class): Model
    {
        return new $class($this->connection);
    }

}