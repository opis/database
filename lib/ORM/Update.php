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

use Opis\Database\Connection;
use Opis\Database\SQL\UpdateStatement;

class Update extends UpdateStatement
{
    /** @var    Select */
    protected $select;

    /** @var    Connection */
    protected $connection;

    /**
     * Constructor
     *
     * @param   Slect       $select
     * @param   Connection  $connection
     */
    public function __construct(Select $select, Connection $connection)
    {
        parent::__construct($select->getCompiler(), $select->getTables(), $select->getWhereClause());
        $this->joins = $select->getJoinClauses();
        $this->connection = $connection;
        $this->select = $select;
    }

    /**
     * @param   array   $columns
     *
     * @return  int
     */
    public function update(array $columns, $softDelete = false)
    {
        if ($softDelete && $this->select->supportsSoftDeletes()) {

            if (!$this->select->isSetInlcudeSoftDeletes()) {
                $this->where('deleted_at')->isNull();
            } elseif ($this->select->isSetOnlySoftDeleted()) {
                $this->where('deleted_at')->notNull();
            }
        }
        
        if (!$softDelete && $this->select->supportsTimestamps()) {
            $columns['updated_at'] = date($this->select->getModel()->getDateFormat());
        }

        parent::set($columns);
        return $this->connection->count((string) $this, $this->compiler->getParams());
    }
}
