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

abstract class Relation
{
    protected $queryCallback;
    protected $entityClass;
    protected $withSoftDeletes = false;
    protected $onlySoftDeletes = false;
    protected $eagerLoad = [];
    protected $foreignKey;

    /**
     * EntityRelation constructor.
     * @param string $entityClass
     * @param string|null $foreignKey
     */
    public function __construct(string $entityClass, string $foreignKey = null)
    {
        $this->entityClass = $entityClass;
        $this->foreignKey = $foreignKey;
    }

    /**
     * @param callable $callback
     * @return Relation
     */
    public function query(callable $callback): self
    {
        $this->queryCallback = $callback;
        return $this;
    }

    /**
     * @param bool $value
     * @return Relation
     */
    public function withSoftDeletes(bool $value = true): self
    {
        $this->withSoftDeletes = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return Relation
     */
    public function onlySoftDeletes(bool $value = true):self
    {
        $this->onlySoftDeletes = $value;
        return $this;
    }

    /**
     * @param array $relations
     * @return Relation
     */
    public function eagerLoad(array $relations): self
    {
        $this->eagerLoad = $relations;
        return $this;
    }

    /**
     * @param DataMapper $data
     * @return mixed
     */
    abstract public function getResult(DataMapper $data, callable $callback = null);
}