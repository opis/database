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

use DateTime;
use Opis\Database\Entity;
use RuntimeException;
use Opis\Database\EntityManager;

class DataMapper
{
    /** @var array  */
    protected $rawColumns;

    /** @var array  */
    protected $columns = [];

    /** @var EntityManager  */
    protected $manager;

    /** @var EntityMapper  */
    protected $mapper;

    /** @var bool  */
    protected $isReadOnly;

    /** @var bool  */
    protected $isNew;

    /** @var array */
    protected $modified = [];

    /** @var array */
    protected $relations = [];

    public function __construct(EntityManager $entityManager, EntityMapper $entityMapper, array $columns, bool $isReadOnly, bool $isNew)
    {
        $this->manager = $entityManager;
        $this->mapper = $entityMapper;
        $this->rawColumns = $columns;
        $this->isReadOnly = $isReadOnly;
        $this->isNew = $isNew;
    }

    public function getEntityManager(): EntityManager
    {
        return $this->manager;
    }

    public function getEntityMapper(): EntityMapper
    {
        return $this->mapper;
    }

    public function getColumn(string $name)
    {
        if(array_key_exists($name, $this->columns)){
            return $this->columns[$name];
        }

        if(!array_key_exists($name, $this->rawColumns)){
            throw new RuntimeException("Unknown column '$name'");
        }

        $value = $this->rawColumns[$name];
        $casts = $this->mapper->getTypeCasts();

        if(isset($casts[$name])){
            $value = $this->castGet($value, $casts[$name]);
        }

        if($name === $this->mapper->getPrimaryKey()){
            return $this->columns[$name] = $value;
        }

        $accessors = $this->mapper->getAccessors();

        if(isset($accessors[$name])){
            $value = $accessors[$name]($value);
        }

        return $this->columns[$name] = $value;
    }

    public function setColumn(string $name, $value)
    {
        if($this->isReadOnly){
            throw new RuntimeException("This record is readonly");
        }

        $casts = $this->mapper->getTypeCasts();
        $setters = $this->mapper->getSetters();

        if(isset($casts[$name])){
            $value = $this->castSet($value, $casts[$name]);
        }

        if(isset($setters[$name])){
            $value = $setters[$name]($value);
        }

        $this->modified[$name] = 1;
        unset($this->columns[$name]);
        $this->rawColumns[$name] = $value;
    }

    public function getRelation(string $name, callable $callback = null)
    {
        if(array_key_exists($name, $this->relations)){
            return $this->relations[$name];
        }

        $relations = $this->mapper->getRelations();

        if(!isset($relations[$name])){
            throw new RuntimeException("Unknown relation '$name'");
        }

        return $this->relations[$name] = $relations[$name]->getResult($this, $callback);
    }

    /**
     * @param $value
     * @param string $cast
     * @return mixed
     */
    protected function castGet($value, string $cast)
    {
        $originalCast = $cast;

        if($cast[0] === '?'){
            if($value === null){
                return null;
            }
            $cast = substr($cast, 1);
        }

        switch ($cast){
            case 'int':
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'bool':
            case 'boolean':
                return (bool) $value;
            case 'string':
                return (string) $value;
            case 'date':
                return DateTime::createFromFormat($this->mapper->getDateFormat(), $value);
            case 'json':
                return json_decode($value);
            case 'json-assoc':
                return json_decode($value, true);
        }

        throw new RuntimeException("Invalid cast type '$originalCast'");
    }

    /**
     * @param $value
     * @param string $cast
     * @return float|int|string
     */
    protected function castSet($value, string $cast)
    {
        $originalCast = $cast;

        if($cast[0] === '?'){
            if($value === null){
                return null;
            }
            $cast = substr($cast, 1);
        }

        switch ($cast){
            case 'int':
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'bool':
            case 'boolean':
                return (int) $value;
            case 'string':
                return (string) $value;
            case 'date':
                return $value;
            case 'json':
            case 'json-assoc':
                return json_encode($value);
        }

        throw new RuntimeException("Invalid cast type '$originalCast'");
    }

}