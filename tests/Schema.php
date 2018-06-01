<?php
/* ===========================================================================
 * Copyright 2013-2018 Opis
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

use Opis\Database\Schema\CreateTable;
use Opis\Database\Schema as BaseSchema;

class Schema extends BaseSchema
{
    public function create(string $table, callable $callback)
    {
        $compiler = $this->connection->schemaCompiler();

        $schema = new CreateTable($table);

        $callback($schema);

        return implode("\n", array_map(function($value){
            return $value['sql'];
        }, $compiler->create($schema)));
    }
}