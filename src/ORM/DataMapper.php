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

namespace Opis\Database\ORM;

use Opis\Database\ORM\Entity;

interface DataMapper
{
    /**
     * @return bool
     */
    public function isNew(): bool;

    /**
     * @return bool
     */
    public function isReadOnly(): bool;

    /**
     * @return bool
     */
    public function isDeleted(): bool;

    /**
     * @return bool
     */
    public function wasModified(): bool;

    /**
     * @return array
     */
    public function getRawColumns(): array;

    /**
     * @return string[]
     */
    public function getModifiedColumns(): array;

    /**
     * @param string $name
     * @return mixed
     */
    public function getColumn(string $name): mixed;

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setColumn(string $name, mixed $value): static;

    /**
     * @param string $name
     * @return $this
     */
    public function clearColumn(string $name): static;

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setRawColumn(string $name, mixed $value): static;

    /**
     * @param string $name
     * @param callable|null $callback
     * @return mixed
     */
    public function getRelated(string $name, ?callable $callback = null): mixed;

    /**
     * @param string $relation
     * @param Entity|null $entity
     * @return $this
     */
    public function setRelated(string $relation, ?Entity $entity = null): static;

    /**
     * @param string $name
     * @param bool $loaders
     * @return $this
     */
    public function clearRelated(string $name, bool $loaders = false): static;

    /**
     * @param string $relation
     * @param Entity $entity
     * @return $this
     */
    public function link(string $relation, Entity $entity): static;

    /**
     * @param string $relation
     * @param Entity $entity
     * @return $this
     */
    public function unlink(string $relation, Entity $entity): static;

    /**
     * @param array $columns
     * @return $this
     */
    public function assign(array $columns): static;

    /**
     * Force hydration
     * @return $this
     */
    public function stale(): static;
}