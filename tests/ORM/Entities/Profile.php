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
use Opis\Database\ORM\{
    MappableEntity, EntityMapper
};
use Opis\Database\ORM\Entity;
use function Opis\Database\Test\unique_id;

class Profile extends Entity implements MappableEntity
{
    public function id(): string
    {
        return $this->orm()->getColumn('id');
    }

    public function city(): string
    {
        return $this->orm()->getColumn('city');
    }

    public function setCity(string $city): self
    {
        $this->orm()->setColumn('city', $city);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function mapEntity(EntityMapper $mapper): void
    {
        $mapper->primaryKeyGenerator(function(){
            return unique_id();
        });

        //$mapper->relation('user')->belongsTo(User::class);
    }
}