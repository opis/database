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

use Opis\Database\ORM\Internal\RelationFactory;

interface EntityMapper
{
    /**
     * @param string $name
     * @return $this
     */
    public function entityName(string $name): static;

    /**
     * @param string $table
     * @return $this
     */
    public function table(string $table): static;

    /**
     * @param string ...$primaryKey
     * @return $this
     */
    public function primaryKey(string ...$primaryKey): static;

    /**
     * @param callable $callback
     * @return $this
     */
    public function primaryKeyGenerator(callable $callback): static;

    /**
     * @param string $sequence
     * @return $this
     */
    public function sequence(string $sequence): static;

    /**
     * @param string $column
     * @param callable $callback
     * @return $this
     */
    public function getter(string $column, callable $callback): static;

    /**
     * @param string $column
     * @param callable $callback
     * @return $this
     */
    public function setter(string $column, callable $callback): static;

    /**
     * @param string $name
     * @return RelationFactory
     */
    public function relation(string $name): RelationFactory;

    /**
     * @param array $casts
     * @return $this
     */
    public function cast(array $casts): static;

    /**
     * @param bool $value
     * @param string|null $column
     * @return $this
     */
    public function useSoftDelete(bool $value = true, ?string $column = null): static;

    /**
     * @param bool $value
     * @param string|null $created_at
     * @param string|null $updated_at
     * @return $this
     */
    public function useTimestamp(bool $value = true, ?string $created_at = null, ?string $updated_at = null): static;

    /**
     * @param string[] $columns
     * @return $this
     */
    public function assignable(array $columns): static;

    /**
     * @param string[] $columns
     * @return $this
     */
    public function guarded(array $columns): static;

    /**
     * @param string $name
     * @param callable $callback
     * @return $this
     */
    public function filter(string $name, callable $callback): static;

    /**
     * @param string $event
     * @param callable $callback
     * @return $this
     */
    public function on(string $event, callable $callback): static;
}