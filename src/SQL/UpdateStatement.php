<?php
/* ===========================================================================
 * Copyright 2018 Zindex Software
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

namespace Opis\Database\SQL;

class UpdateStatement extends BaseStatement
{
    /**
     * UpdateStatement constructor.
     * @param string|array $table
     * @param SQLStatement|null $statement
     */
    public function __construct($table, SQLStatement $statement = null)
    {
        if (!is_array($table)) {
            $table = [$table];
        }

        parent::__construct($statement);

        $this->sql->addTables($table);
    }

    /**
     * @param   array $columns
     */
    public function set(array $columns)
    {
        $this->sql->addUpdateColumns($columns);
    }

}
