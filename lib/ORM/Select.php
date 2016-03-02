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
use Opis\Database\SQL\Delete;
use Opis\Database\SQL\Compiler;
use Opis\Database\SQL\WhereClause;
use Opis\Database\SQL\SelectStatement;

class Select extends SelectStatement
{
    /* @var     Model */
    protected $model;

    /** @var    bool */
    protected $locked = false;

    /** @var    bool */
    protected $supportsSoftDeletes;

    /** @var    bool */
    protected $supportsTimestamps;

    /** @var    bool */
    protected $inlcudeSoftDeletes = false;

    /** @var    bool */
    protected $onlySoftDeleted = false;

    /**
     * Constructor
     * 
     * @param   Model           $model
     * @param   Compiler        $compiler
     * @param   string|array    $tables         (optional)
     * @param   boolean         $softdelete     (optional)
     */
    public function __construct(Model $model, Compiler $compiler, $tables = null, WhereClause $clause = null)
    {
        $this->model = $model;

        if ($tables === null) {
            $tables = $model->getTable();
        }

        $this->supportsSoftDeletes = $model->supportsSoftDeletes();
        $this->supportsTimestamps = $model->supportsTimestamps();

        parent::__construct($compiler, $tables, $clause);
    }

    /**
     * 
     * @return  Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return  \Opis\Database\SQL\Compiler
     */
    public function getCompiler()
    {
        return $this->compiler;
    }

    /**
     * 
     * @return  bool
     */
    public function supportsSoftDeletes()
    {
        return $this->supportsSoftDeletes;
    }

    /**
     * 
     * @return  bool
     */
    public function supportsTimestamps()
    {
        return $this->supportsTimestamps;
    }

    /**
     * 
     * @return  bool
     */
    public function isSetInlcudeSoftDeletes()
    {
        return $this->inlcudeSoftDeletes;
    }

    /**
     * 
     * @return  bool
     */
    public function isSetOnlySoftDeleted()
    {
        return $this->onlySoftDeleted;
    }

    /**
     * @return  bool
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * 
     * @return  $this
     */
    public function withSoftDeleted()
    {
        $this->inlcudeSoftDeletes = true;
        return $this;
    }

    /**
     * 
     * @return  $this
     */
    public function onlySoftDeleted()
    {
        $this->onlySoftDeleted = $this->inlcudeSoftDeletes = true;
        return $this;
    }

    /**
     * @param   string|array    $tables
     *
     * @return  $this
     */
    public function from($tables)
    {
        if (!is_array($tables)) {
            $tables = array($tables);
        }

        $this->tables = $tables;
        return $this;
    }

    /**
     * @return  $this
     */
    public function lock()
    {
        $this->locked = true;
        return $this;
    }

    /**
     * @param   array   $columns    (optional)
     *
     * @return  $this
     */
    public function select($columns = array())
    {
        $this->sql = null;
        return parent::select($columns);
    }

    /**
     * @param   Connection  $connection
     *
     * @return  Delete
     */
    public function toDelete(Connection $connection)
    {
        return new Delete($connection, $this->compiler, $this->tables, $this->joins, $this->whereClause);
    }

    /**
     * @param   Connection  $connection
     *
     * @return  Update
     */
    public function toUpdate(Connection $connection)
    {
        return new Update($this, $connection);
    }

    /**
     * @return  string
     */
    public function __toString()
    {
        if ($this->sql === null) {
            if ($this->supportsSoftDeletes) {
                if (!$this->inlcudeSoftDeletes) {
                    $this->where('deleted_at')->isNull();
                } elseif ($this->onlySoftDeleted) {
                    $this->where('deleted_at')->notNull();
                }
            }
            $this->sql = $this->compiler->select($this);
        }
        return $this->sql;
    }
}
