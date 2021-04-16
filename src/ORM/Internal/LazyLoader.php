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

use Opis\Database\ORM\Entity;
use Opis\Database\ORM\ForeignKey;

class LazyLoader
{
    protected EntityQuery $query;
    protected ForeignKey $foreignKey;
    protected bool $inverse;
    protected bool $hasMany;
    protected ?array $keys = null;

    /** @var null|Entity[] */
    protected ?array $results = null;

    /**
     * LazyLoader constructor.
     * @param EntityQuery $query
     * @param ForeignKey $foreignKey
     * @param bool $inverse
     * @param bool $hasMany
     * @param bool $immediate
     */
    public function __construct(
        EntityQuery $query,
        ForeignKey $foreignKey,
        bool $inverse,
        bool $hasMany,
        bool $immediate
    ) {
        $this->query = $query;
        $this->foreignKey = $foreignKey;
        $this->inverse = $inverse;
        $this->hasMany = $hasMany;

        if ($immediate) {
            $this->loadResults();
        }
    }

    /**
     * @param DataMapper $data
     * @return null|Entity|Entity[]
     */
    public function getResult(DataMapper $data): null|array|Entity
    {
        $results = $this->loadResults();

        if ($this->inverse) {
            $check = $this->foreignKey->extractValue($data->getRawColumns(), true);
        } else {
            $check = $this->foreignKey->getValue($data->getRawColumns(), true);
        }

        if ($this->hasMany) {
            $all = [];
            foreach ($this->keys as $index => $item) {
                if ($item === $check) {
                    $all[] = $results[$index];
                }
            }
            return $all;
        }

        foreach ($this->keys as $index => $item) {
            if ($item === $check) {
                return $results[$index];
            }
        }

        return null;
    }

    /**
     * @return Entity[]
     */
    protected function loadResults(): array
    {
        if ($this->results === null) {
            $this->results = $this->query->all();
            $this->keys = [];
            $proxy = Proxy::instance();
            foreach ($this->results as $result) {
                if ($this->inverse) {
                    $this->keys[] = $this->foreignKey->getValue($proxy->getEntityColumns($result), true);
                } else {
                    $this->keys[] = $this->foreignKey->extractValue($proxy->getEntityColumns($result), true);
                }
            }
        }

        return $this->results;
    }
}