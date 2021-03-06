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

use Opis\Database\ResultSet;
use Opis\Database\Connection as BaseConnection;

class Connection extends BaseConnection
{
    private string $result = '';

    public function __construct(?string $driver)
    {
        parent::__construct('');
        $this->driver = $driver;
        //$this->setWrapperFormat('`%s`');
    }

    public function query(string $sql, array $params = []): ResultSet
    {
        $this->result = $this->replaceParams($sql, $params);
        return new ResultSet(null);
    }

    public function column(string $sql, array $params = []): mixed
    {
        $this->result = $this->replaceParams($sql, $params);
        return null;
    }

    public function count(string $sql, array $params = []): int
    {
        $this->result = $this->replaceParams($sql, $params);
        return 0;
    }

    public function command(string $sql, array $params = []): bool
    {
        $this->result = $this->replaceParams($sql, $params);
        return true;
    }

    public function getResult(): string
    {
        return $this->result;
    }
}