<?php
/* ===========================================================================
 * Copyright 2018-2021 Zindex Software
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

namespace Opis\Database\Test;

use Opis\Database\Schema\Blueprint;
use Opis\Database\Schema as BaseSchema;

class Schema extends BaseSchema
{
    private string $result = '';

    public function create(string $table, callable $callback): void
    {
        $compiler = $this->connection->schemaCompiler();

        $schema = new Blueprint($table);

        $callback($schema);

        $this->result = $this->implode($compiler->create($schema));
    }

    public function alter(string $table, callable $callback): void
    {
        $compiler = $this->connection->schemaCompiler();

        $schema = new Blueprint($table, true);

        $callback($schema);

        $this->result = $this->implode($compiler->alter($schema));
    }

    public function renameTable(string $table, string $name): void
    {
        $result = $this->connection->schemaCompiler()->renameTable($table, $name);

        $this->result = $this->implode($result);
    }

    public function drop(string $table): void
    {
        $compiler = $this->connection->schemaCompiler();

        $this->result = $this->implode($compiler->drop($table));
    }

    public function truncate(string $table): void
    {
        $compiler = $this->connection->schemaCompiler();

        $this->result = $this->implode($compiler->truncate($table));
    }

    public function createView(string $view, string|array $table, callable $callback): void
    {
        $connection = $this->connection;

        $select = $this->createViewSelect($table, $callback);

        $result = $connection->schemaCompiler()->createView($view, $select->sql, $select->params);

        $this->result = $this->implode($result);
    }

    public function dropView(string $view): void
    {
        $compiler = $this->connection->schemaCompiler();

        $this->result = $this->implode($compiler->dropView($view));
    }

    public function getResult(): string
    {
        return $this->result;
    }

    protected function implode(array $list, string $delimiter = "\n"): string
    {
        if (isset($list['sql'])) {
            // only one command
            $list = [$list];
        }
        return implode($delimiter, array_map(static fn(array $v) => $v['sql'], $list));
    }
}