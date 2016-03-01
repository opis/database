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

use Exception;
use Opis\Database\Model;
use Opis\Database\Database;
use Opis\Database\Connection;
use Opis\Database\ORM\Select;
use Opis\Database\ORM\Relation;
use Opis\Database\ORM\LazyLoader;

class BelongsToMany extends Relation
{
    /** @var    string */
    protected $junctionTable;

    /** @var    string */
    protected $junctionKey;

    /**
     * Constructor
     *
     * @param   Connection  $connection
     * @param   Model       $owner
     * @param   Model       $model
     * @param   string|null $foreignKey     (optional)
     * @param   string|null $junctionTable  (optional)
     * @param   string|null $junctionKey    (optional)
     */
    public function __construct(Connection $connection, Model $owner, Model $model, $foreignKey = null, $junctionTable = null, $junctionKey = null)
    {
        $this->junctionTable = $junctionTable;
        $this->junctionKey = $junctionKey;

        parent::__construct($connection, $owner, $model, $foreignKey);
    }

    /**
     * @return  string
     */
    protected function getJunctionTable()
    {
        if ($this->junctionTable === null) {
            $table = array($this->owner->getTable(), $this->model->getTable());
            sort($table);
            $this->junctionTable = implode('_', $table);
        }

        return $this->junctionTable;
    }

    /**
     * @return  string
     */
    protected function getJunctionKey()
    {
        if ($this->junctionKey === null) {
            $this->junctionKey = $this->model->getForeignKey();
        }

        return $this->junctionKey;
    }

    /**
     * Link records
     * 
     * @param   mixed   $item
     */
    public function link($item)
    {
        if (!is_array($item)) {
            $item = array($item);
        }

        $table = $this->getJunctionTable();
        $col1 = $this->getForeignKey();
        $col2 = $this->getJunctionKey();
        $val1 = $this->owner->{$this->owner->getPrimaryKey()};

        $db = new Database($this->connection);

        foreach ($item as $record) {
            $val2 = $record;
            if ($record instanceof $this->model) {
                $val2 = $record->{$record->getPrimaryKey()};
            }

            try {
                $db->insert(array(
                        $col1 => $val1,
                        $col2 => $val2,
                    ))
                    ->into($table);
            } catch (Exception $ex) {
                
            }
        }
    }

    /**
     * Unlink records
     * 
     * @param   mixed   $item
     */
    public function unlink($item)
    {
        if (!is_array($item)) {
            $item = array($item);
        }

        $table = $this->getJunctionTable();
        $col1 = $this->getForeignKey();
        $col2 = $this->getJunctionKey();
        $val1 = $this->owner->{$this->owner->getPrimaryKey()};
        $val2 = array();

        $db = new Database($this->connection);

        foreach ($item as $record) {
            if ($record instanceof $this->model) {
                $val2[] = $record->{$record->getPrimaryKey()};
                continue;
            }
            $val2[] = $record;
        }

        $db->from($table)
            ->where($col1)->is($val1)
            ->andWhere($col2)->in($val2)
            ->delete();
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

        $junctionTable = $this->getJunctionTable();
        $junctionKey = $this->getJunctionKey();
        $joinTable = $this->model->getTable();
        $joinColumn = $this->model->getPrimaryKey();

        $select = new Select($this->model, $this->compiler, $junctionTable);

        $linkKey = 'hidden_' . $junctionTable . '_' . $fk;

        $select->join($joinTable, function ($join) use ($junctionTable, $junctionKey, $joinTable, $joinColumn) {
                $join->on($junctionTable . '.' . $junctionKey, $joinTable . '.' . $joinColumn);
            })
            ->where($junctionTable . '.' . $fk)->in($ids)
            ->select(array($joinTable . '.*', $junctionTable . '.' . $fk => $linkKey));

        if ($callback !== null) {
            $callback($select);
        }

        $query = (string) $select;
        $params = $select->getCompiler()->getParams();

        return new LazyLoader($this->connection, $query, $params, $with, $immediate, $this->isReadOnly, $this->hasMany(), get_class($this->model), $linkKey, $pk);
    }

    /**
     * Build query
     * 
     * @return  Select
     */
    protected function buildQuery()
    {
        $self = $this;
        $junctionTable = $this->getJunctionTable();
        $junctionKey = $this->getJunctionKey();
        $joinTable = $this->model->getTable();
        $joinColumn = $this->model->getPrimaryKey();
        $foreignKey = $this->getForeignKey();

        return $this->query
                ->from($junctionTable)
                ->join($joinTable, function ($join) use ($junctionTable, $junctionKey, $joinTable, $joinColumn) {
                    $join->on($junctionTable . '.' . $junctionKey, $joinTable . '.' . $joinColumn);
                })
                ->where($junctionTable . '.' . $foreignKey)->is($this->owner->{$this->owner->getPrimaryKey()})
                ->lock();
    }

    /**
     * @return  Model
     */
    public function getResult()
    {
        $columns = array($this->model->getTable() . '.*');

        return $this->query($columns)
                ->fetchClass(get_class($this->model), array($this->isReadOnly, $this->connection))
                ->all();
    }
}
