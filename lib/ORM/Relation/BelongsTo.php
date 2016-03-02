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

namespace Opis\Database\ORM\Relation;

use Opis\Database\Model;
use Opis\Database\ORM\Select;
use Opis\Database\ORM\Relation;
use Opis\Database\ORM\LazyLoader;

class BelongsTo extends Relation
{

    /**
     * @return  bool
     */
    public function hasMany()
    {
        return false;
    }

    /**
     * @param   Model   $model
     * @param   string  $name
     *
     * @return  string
     */
    public function getRelatedColumn(Model $model, $name)
    {
        return $this->getForeignKey();
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

        $select->where($pk)->in($ids);

        if ($callback !== null) {
            $callback($select);
        }

        $query = (string) $select;
        $params = $select->getCompiler()->getParams();

        return new LazyLoader($this->connection, $query, $params, $with, $immediate, $this->isReadOnly, $this->hasMany(), get_class($this->model), $pk, $fk);
    }

    /**
     * @return  string
     */
    public function getForeignKey()
    {
        if ($this->foreignKey === null) {
            $this->foreignKey = $this->model->getForeignKey();
        }

        return $this->foreignKey;
    }

    /**
     * Build query
     * 
     * @return  Select
     */
    protected function buildQuery()
    {
        return $this->query->where($this->model->getPrimaryKey())->is($this->owner->{$this->getForeignKey()});
    }

    /**
     * @return  Model
     */
    public function getResult()
    {
        return $this->query()
                ->fetchClass(get_class($this->model), array($this->isReadOnly, $this->connection))
                ->first();
    }
}
