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


class EntityMapper
{
    protected $entityClass;
    protected $className;
    protected $table;
    protected $primaryKey = 'id';
    protected $primaryKeyGenerator;

    public function __construct(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }

    public function setTable(string $table)
    {
        $this->table = $table;
    }

    public function setPrimaryKey(string $primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    /**
     * Get the entity's table
     *
     * @return  string
     */
    public function getTable(): string
    {
        if ($this->table === null) {
            $this->table = strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $this->getClassShortName())) . 's';
        }

        return $this->table;
    }

    public function getPrimaryKey(): string 
    {
        return $this->primaryKey;
    }

    /**
     * Get the name of the foreign key of the entity's table
     *
     * @return  string
     */
    public function getForeignKey(): string
    {
        return str_replace('-', '_', strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $this->getClassShortName()))) . '_id';
    }

    /**
     * Returns the short class name of the entity
     *
     * @return  string
     */
    protected function getClassShortName()
    {
        if ($this->className === null) {
            $name = $this->entityClass;

            if (false !== $pos = strrpos($name, '\\')) {
                $name = substr($name, $pos + 1);
            }

            $this->className = $name;
        }

        return $this->className;
    }
}