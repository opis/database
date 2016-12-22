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

use Opis\Database\Entity;

class LazyLoader
{
    /** @var EntityQuery */
    protected $query;

    /** @var string  */
    protected $primaryKey;

    /** @var string */
    protected $foreignKey;

    /** @var bool */
    protected $hasMany;

    /** @var null|Entity[] */
    protected $results;

    /** @var null|array */
    protected $keys;

    /**
     * LazyLoader constructor.
     * @param EntityQuery $query
     * @param string $primaryKey
     * @param string $foreignKey
     * @param bool $hasMany
     * @param bool $immediate
     */
    public function __construct(EntityQuery $query, string $primaryKey, string $foreignKey, bool $hasMany, bool $immediate)
    {
        $this->query = $query;
        $this->primaryKey = $primaryKey;
        $this->foreignKey = $foreignKey;
        $this->hasMany = $hasMany;

        if($immediate){
            $this->loadResults();
        }
    }

    /**
     * @param DataMapper $data
     * @return null|Entity|Entity[]
     */
    public function getResult(DataMapper $data)
    {
        $results = $this->loadResults();
        $pk = $data->getColumn($this->primaryKey);
        
        if($this->hasMany){
            $all = [];
            foreach ($this->keys as $key => $id){
                if($id === $pk){
                    $all[] = $results[$key];
                }
            }
            return $all;
        }

        foreach ($this->keys as $key => $id){
            if($id === $pk){
                return $results[$key];
            }
        }

        return null;
    }

    /**
     * @return Entity[]
     */
    protected function loadResults()
    {
        if($this->results === null){
            $this->results = $this->query->all();
            $list = [];
            $key = $this->foreignKey;
            $setup = function () use(&$list, $key){
                $list[] = $this->dataMapperArgs[2][$key];
            };

            foreach ($this->results as $result){
                $setup->call($result);
            }

            $this->keys = $list;
        }

        return $this->results;
    }
}