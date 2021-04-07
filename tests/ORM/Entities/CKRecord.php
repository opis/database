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
    EntityMapper, MappableEntity
};
use Opis\Database\ORM\Entity;

class CKRecord extends Entity implements MappableEntity
{
    public function getData(): string
    {
        return $this->orm()->getColumn('data');
    }

    public function setData(string $data)
    {
        $this->orm()->setColumn('data', $data);
    }

    public function getCKRelated(): array
    {
        return $this->orm()->getRelated('ck_related');
    }

    /**
     * @inheritDoc
     */
    public static function mapEntity(EntityMapper $mapper): void
    {
        $mapper->entityName('ck_record');
        $mapper->table('ck_records');
        $mapper->primaryKey('key1', 'key2');
        $mapper->primaryKeyGenerator(function(){
            static $counter = null;
            if ($counter === null) {
                $counter = 0;
            }
            $counter++;
           return ['key1' => 2, 'key2' => $counter];
        });
        $mapper->relation('ck_related')->hasMany(CKRelated::class);
    }

}