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

namespace Opis\Database\ORM\Internal;

use Opis\Database\ORM\Relations\{
    BelongsTo, HasOneOrMany, ShareOneOrMany
};

class RelationFactory
{
    /** @var  callable */
    protected $callback;
    protected string $name;

    /**
     * RelationFactory constructor.
     * @param string $name
     * @param callable $callback
     */
    public function __construct(string $name, callable $callback)
    {
        $this->name = $name;
        $this->callback = $callback;
    }

    /**
     * @param string $entityClass
     * @param ForeignKey|null $foreignKey
     * @return Relation
     */
    public function hasOne(string $entityClass, ?ForeignKey $foreignKey = null): Relation
    {
        return ($this->callback)($this->name, new HasOneOrMany($entityClass, $foreignKey));
    }

    /**
     * @param string $entityClass
     * @param ForeignKey|null $foreignKey
     * @return Relation
     */
    public function hasMany(string $entityClass, ?ForeignKey $foreignKey = null): Relation
    {
        return ($this->callback)($this->name, new HasOneOrMany($entityClass, $foreignKey, true));
    }

    /**
     * @param string $entityClass
     * @param ForeignKey|null $foreignKey
     * @return Relation
     */
    public function belongsTo(string $entityClass, ?ForeignKey $foreignKey = null): Relation
    {
        return ($this->callback)($this->name, new BelongsTo($entityClass, $foreignKey));
    }

    /**
     * @param string $entityClass
     * @param ForeignKey|null $foreignKey
     * @param Junction|null $junction
     * @return Relation
     */
    public function shareOne(string $entityClass, ?ForeignKey $foreignKey = null, ?Junction $junction = null): Relation
    {
        return ($this->callback)($this->name, new ShareOneOrMany($entityClass, $foreignKey, $junction));
    }

    /**
     * @param string $entityClass
     * @param ForeignKey|null $foreignKey
     * @param Junction|null $junction
     * @return Relation
     */
    public function shareMany(string $entityClass, ?ForeignKey $foreignKey = null, ?Junction $junction = null): Relation
    {
        return ($this->callback)($this->name, new ShareOneOrMany($entityClass, $foreignKey, $junction, true));
    }
}