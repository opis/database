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

namespace Opis\Database\ORM\Relations;

use Opis\Database\EntityManager;
use Opis\Database\ORM\{Entity, Query};
use Opis\Database\SQL\SQLStatement;
use Opis\Database\ORM\Internal\{
    DataMapper, EntityMapper, EntityQuery, LazyLoader, Proxy, Relation
};

class BelongsTo extends Relation
{
    /**
     * @param DataMapper $owner
     * @param Entity|null $entity
     * @return $this
     */
    public function addRelatedEntity(DataMapper $owner, ?Entity $entity = null): static
    {
        if ($entity === null) {
            $columns = [];
            $mapper = $owner->getEntityManager()->resolveEntityMapper($this->entityClass);
        } else {
            $related = Proxy::instance()->getDataMapper($entity);
            $mapper = $related->getEntityMapper();
            $columns = $related->getRawColumns();
        }

        if ($this->foreignKey === null) {
            $this->foreignKey = $mapper->getForeignKey();
        }

        foreach ($this->foreignKey->getValue($columns, true) as $fk_column => $fk_value) {
            $owner->setColumn($fk_column, $fk_value);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLazyLoader(EntityManager $manager, EntityMapper $owner, array $options): LazyLoader
    {
        $related = $manager->resolveEntityMapper($this->entityClass);

        if ($this->foreignKey === null) {
            $this->foreignKey = $related->getForeignKey();
        }

        $ids = [];
        foreach ($options['results'] as $result) {
            foreach ($this->foreignKey->getInverseValue($result, true) as $pk_col => $pk_val) {
                $ids[$pk_col][] = $pk_val;
            }
        }

        $statement = new SQLStatement();
        $select = new EntityQuery($manager, $related, $statement);

        foreach ($ids as $col => $val) {
            $val = array_unique($val);
            if (count($val) > 1) {
                $select->where($col)->in($val);
            } else {
                $select->where($col)->is(reset($val));
            }
        }

        if ($options['callback'] !== null) {
            $options['callback'](new Query($statement));
        }

        $select->with($options['with'], $options['immediate']);

        return new LazyLoader($select, $this->foreignKey, true, false, $options['immediate']);
    }

    /**
     * @inheritDoc
     */
    public function getResult(DataMapper $data, ?callable $callback = null): mixed
    {
        $manager = $data->getEntityManager();
        $related = $manager->resolveEntityMapper($this->entityClass);

        if ($this->foreignKey === null) {
            $this->foreignKey = $related->getForeignKey();
        }

        $statement = new SQLStatement();
        $select = new EntityQuery($manager, $related, $statement);

        foreach ($this->foreignKey->getInverseValue($data->getRawColumns(), true) as $pk_column => $pk_value) {
            $select->where($pk_column)->is($pk_value);
        }

        if ($this->queryCallback !== null || $callback !== null) {
            $query = $select;//new Query($statement);
            if ($this->queryCallback !== null) {
                ($this->queryCallback)($query);
            }
            if ($callback !== null) {
                $callback($query);
            }
        }

        return $select->get();
    }
}