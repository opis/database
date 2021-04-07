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

namespace Opis\Database\Test\ORM\Entities;

use Opis\Database\ORM\Entity;
use Opis\Database\ORM\DataMapper;
use Opis\Database\ORM\EntityMapper;
use Opis\Database\ORM\MappableEntity;

class AutomatedEntity2 extends Entity implements MappableEntity
{
    public function getCreatedAt(): \DateTime
    {
        return $this->orm()->getColumn('c_at');
    }

    public function getUpdatedAt()
    {
        return $this->orm()->getColumn('u_at');
    }

    public function getData(): string
    {
        return $this->orm()->getColumn('data');
    }

    public function setData(string $data)
    {
        $this->orm()->setColumn('data', $data);
    }

    /**
     * @inheritDoc
     */
    public static function mapEntity(EntityMapper $mapper): void
    {
        $mapper->table('automated_entity_2');
        $mapper->cast([
            'd_at' => '?date',
            'c_at' => 'date',
            'u_at' => '?date',
        ]);

        $mapper->useSoftDelete(true, 'd_at');
        $mapper->useTimestamp(true, 'c_at', 'u_at');
    }
}