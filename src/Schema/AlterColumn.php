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

namespace Opis\Database\Schema;

class AlterColumn extends BaseColumn
{
    /** @var string */
    protected $table;

    /**
     * AlterColumn constructor.
     * @param AlterTable $table
     * @param string $name
     * @param string|null $type
     */
    public function __construct(AlterTable $table, string $name, string $type = null)
    {
        $this->table = $table;
        parent::__construct($name, $type);
    }

    /**
     * @return  string
     */
    public function getTable(): string
    {
        return $this->table;
    }


    /**
     * @inheritdoc
     */
    public function defaultValue($value): BaseColumn
    {
        if ($this->get('handleDefault', true)) {
            return parent::defaultValue($value);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function autoincrement(): self
    {
        return $this->set('autoincrement', true);
    }
}
