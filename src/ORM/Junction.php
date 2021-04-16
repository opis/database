<?php
/* ===========================================================================
 * Copyright 2018-2020 Zindex Software
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

class Junction
{
    private string $table;

    /** @var string[] */
    private array $columns;

    /**
     * Junction constructor.
     * @param string $table
     * @param array $columns
     */
    public function __construct(string $table, array $columns)
    {
        $this->table = $table;
        $this->columns = $columns;
    }

    /**
     * @return string
     */
    public function table(): string
    {
        return $this->table;
    }

    /**
     * @return string[]
     */
    public function columns(): array
    {
        return $this->columns;
    }
}