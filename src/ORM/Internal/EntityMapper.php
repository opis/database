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

use Opis\Database\ORM\EntityMapper as EntityMapperInterface;

class EntityMapper implements EntityMapperInterface
{
    protected string $entityClass;
    protected ?string $entityName = null;
    protected ?string $table = null;
    protected bool $readonly = false;
    protected ?PrimaryKey $primaryKey = null;
    protected ?ForeignKey $foreignKey = null;

    /** @var  callable|null */
    protected $primaryKeyGenerator = null;

    /** @var callable[] */
    protected array $getters = [];

    /** @var callable[] */
    protected array $setters = [];

    /** @var array */
    protected array $casts = [];

    /** @var Relation[] */
    protected array $relations = [];

    protected ?string $sequence = null;
    protected bool $softDelete = true;
    protected bool $timestamp = true;
    protected ?array $assignable = null;
    protected ?array $guarded = null;

    /** @var callable[] */
    protected array $filters = [];

    /** @var string */
    protected string $softDeleteColumn = 'deleted_at';

    /** @var string[] */
    protected array $timestampColumns = ['created_at', 'updated_at'];

    protected array $eventHandlers = [];

    /**
     * EntityMapper constructor.
     * @param string $entityClass
     */
    public function __construct(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @inheritDoc
     */
    public function entityName(string $name): static
    {
        $this->entityName = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function table(string $table): static
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function primaryKey(string ...$primaryKey): static
    {
        $this->primaryKey = new PrimaryKey(...$primaryKey);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function primaryKeyGenerator(callable $callback): static
    {
        $this->primaryKeyGenerator = $callback;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function sequence(string $sequence): static
    {
        $this->sequence = $sequence;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getter(string $column, callable $callback): static
    {
        $this->getters[$column] = $callback;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setter(string $column, callable $callback): static
    {
        $this->setters[$column] = $callback;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function relation(string $name): RelationFactory
    {
        return new RelationFactory($name, function ($name, Relation $relation) {
            return $this->relations[$name] = $relation;
        });
    }

    /**
     * @inheritDoc
     */
    public function cast(array $casts): static
    {
        $this->casts = $casts;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function useSoftDelete(bool $value = true, ?string $column = null): static
    {
        $this->softDelete = $value;
        if ($column !== null) {
            $this->softDeleteColumn = $column;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function useTimestamp(
        bool $value = true,
        ?string $created_at = null,
        ?string $updated_at = null
    ): static {
        $this->timestamp = $value;
        if ($created_at !== null) {
            $this->timestampColumns[0] = $created_at;
        }
        if ($updated_at !== null) {
            $this->timestampColumns[1] = $updated_at;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function assignable(array $columns): static
    {
        $this->assignable = $columns;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function guarded(array $columns): static
    {
        $this->guarded = $columns;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filter(string $name, callable $callback): static
    {
        $this->filters[$name] = $callback;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function on(string $event, callable $callback): static
    {
        $this->eventHandlers[$event] = $callback;
        return $this;
    }

    /**
     * Check if marked as readonly
     *
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->readonly;
    }

    /**
     * @inheritDoc
     */
    public function readonly(bool $value = true): static
    {
        $this->readonly = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->entityClass;
    }

    /**
     * Get the entity's table
     *
     * @return  string
     */
    public function getTable(): string
    {
        if ($this->table === null) {
            $this->table = $this->getEntityName() . 's';
        }

        return $this->table;
    }

    /**
     * @return PrimaryKey
     */
    public function getPrimaryKey(): PrimaryKey
    {
        if ($this->primaryKey === null) {
            $this->primaryKey = new PrimaryKey('id');
        }
        return $this->primaryKey;
    }

    /**
     * @return callable|null
     */
    public function getPrimaryKeyGenerator(): ?callable
    {
        return $this->primaryKeyGenerator;
    }

    /**
     * Get the default foreign key
     *
     * @return ForeignKey
     */
    public function getForeignKey(): ForeignKey
    {
        if ($this->foreignKey === null) {
            $pk = $this->getPrimaryKey();
            $prefix = $this->getEntityName();
            $this->foreignKey = new class($pk, $prefix) extends ForeignKey
            {
                /**
                 *  constructor.
                 * @param PrimaryKey $primaryKey
                 * @param string $prefix
                 */
                public function __construct(PrimaryKey $primaryKey, string $prefix)
                {
                    $columns = [];
                    foreach ($primaryKey->columns() as $column) {
                        $columns[$column] = $prefix . '_' . $column;
                    }
                    parent::__construct($columns);
                }
            };
        }

        return $this->foreignKey;
    }

    /**
     * @return string[]
     */
    public function getTypeCasts(): array
    {
        return $this->casts;
    }

    /**
     * @return callable[]
     */
    public function getGetters(): array
    {
        return $this->getters;
    }

    /**
     * @return callable[]
     */
    public function getSetters(): array
    {
        return $this->setters;
    }

    /**
     * @return Relation[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * @return string
     */
    public function getSequence(): string
    {
        if ($this->sequence === null) {
            $this->sequence = $this->getTable() . '_' . $this->getPrimaryKey()->columns()[0] . '_seq';
        }
        return $this->sequence;
    }

    /**
     * @return bool
     */
    public function supportsSoftDelete(): bool
    {
        $deleted_at = $this->softDeleteColumn;
        return $this->softDelete && isset($this->casts[$deleted_at]) && $this->casts[$deleted_at] === '?date';
    }

    /**
     * @return string
     */
    public function getSoftDeleteColumn(): string
    {
        return $this->softDeleteColumn;
    }

    /**
     * @return bool
     */
    public function supportsTimestamp(): bool
    {
        [$created_at, $updated_at] = $this->timestampColumns;
        return $this->timestamp && isset($this->casts[$created_at], $this->casts[$updated_at])
            && $this->casts[$created_at] === 'date' && $this->casts[$updated_at] === '?date';
    }

    /**
     * @return string[]
     */
    public function getTimestampColumns(): array
    {
        return $this->timestampColumns;
    }

    /**
     * @return string[]|null
     */
    public function getAssignableColumns(): ?array
    {
        return $this->assignable;
    }

    /**
     * @return string[]|null
     */
    public function getGuardedColumns(): ?array
    {
        return $this->guarded;
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return callable[]
     */
    public function getEventHandlers(): array
    {
        return $this->eventHandlers;
    }

    /**
     * Returns the entity's name
     *
     * @return  string
     */
    protected function getEntityName(): string
    {
        if ($this->entityName === null) {
            $name = $this->entityClass;

            if (false !== $pos = strrpos($name, '\\')) {
                $name = substr($name, $pos + 1);
            }
            $name = strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $name));
            $name = str_replace('-', '_', $name);
            $this->entityName = $name;
        }

        return $this->entityName;
    }
}