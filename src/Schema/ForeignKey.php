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

class ForeignKey
{
    protected ?string $refTable;
    protected ?array $refColumns;
    protected array $actions = [];
    protected array $columns;

    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    public function getReferencedTable(): string
    {
        return $this->refTable;
    }

    public function getReferencedColumns(): array
    {
        return $this->refColumns;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function references(string $table, string ...$columns): self
    {
        $this->refTable = $table;
        $this->refColumns = $columns;
        return $this;
    }

    public function onDelete(string $action): self
    {
        return $this->addAction('ON DELETE', $action);
    }

    public function onUpdate(string $action): self
    {
        return $this->addAction('ON UPDATE', $action);
    }

    protected function addAction(string $on, string $action): self
    {
        $action = strtoupper($action);

        if (!in_array($action, ['RESTRICT', 'CASCADE', 'NO ACTION', 'SET NULL'])) {
            return $this;
        }

        $this->actions[$on] = $action;
        return $this;
    }
}
