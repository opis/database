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

use Exception;
use Opis\Database\{Connection, EntityManager, ResultSet};
use Opis\Database\SQL\{
    Delete, SQLStatement, Update
};
use Opis\Database\ORM\{
    Traits\AggregateTrait
};

class EntityQuery extends Query
{
    use AggregateTrait;

    protected EntityManager $manager;
    protected EntityMapper $mapper;
    protected bool $locked = false;

    /**
     * EntityQuery constructor.
     * @param EntityManager $entityManager
     * @param EntityMapper $entityMapper
     * @param SQLStatement|null $statement
     */
    public function __construct(
        EntityManager $entityManager,
        EntityMapper $entityMapper,
        ?SQLStatement $statement = null
    ) {
        parent::__construct($statement);
        $this->mapper = $entityMapper;
        $this->manager = $entityManager;
    }

    /**
     * @param string|array $names
     * @return $this
     */
    public function filter(string|array $names): static
    {
        if (!is_array($names)) {
            $names = [$names];
        }

        $query = new Query($this->sql);
        $filters = $this->mapper->getFilters();

        foreach ($names as $name => $data) {
            if (is_int($name)) {
                $name = $data;
                $data = null;
            }
            if (isset($filters[$name])) {
                $filters[$name]($query, $data);
            }
        }

        return $this;
    }

    /**
     * @param array $columns
     * @return object|null
     */
    public function get(array $columns = []): ?object
    {
        $result = $this->query($columns)
            ->fetchAssoc()
            ->first();

        if ($result === false) {
            return null;
        }

        $class = $this->mapper->getClass();

        return new $class($this->manager, $this->mapper, $result, [], $this->isReadOnly(), false);
    }

    /**
     * @param array $columns
     * @return array
     */
    public function all(array $columns = []): array
    {
        $results = $this->query($columns)
            ->fetchAssoc()
            ->all();

        $entities = [];

        $class = $this->mapper->getClass();
        $isReadOnly = $this->isReadOnly();
        $loaders = $this->getLazyLoaders($results);

        foreach ($results as $result) {
            $entities[] = new $class($this->manager, $this->mapper, $result, $loaders, $isReadOnly, false);
        }

        return $entities;
    }

    /**
     * @param bool $force
     * @param array $tables
     * @return int
     */
    public function delete(bool $force = false, array $tables = []): int
    {
        return $this->transaction(function (Connection $connection) use ($tables, $force) {
            if (!$force && $this->mapper->supportsSoftDelete()) {
                return (new Update($connection, $this->mapper->getTable(), $this->sql))->set([
                    $this->mapper->getSoftDeleteColumn() => date($this->manager->getDateFormat()),
                ]);
            }
            return (new Delete($connection, $this->mapper->getTable(), $this->sql))->delete($tables);
        });
    }

    /**
     * @param array $columns
     * @return int
     */
    public function update(array $columns = []): int
    {
        return $this->transaction(function (Connection $connection) use ($columns) {
            if ($this->mapper->supportsTimestamp()) {
                $columns[$this->mapper->getTimestampColumns()[1]] = date($this->manager->getDateFormat());
            }
            return (new Update($connection, $this->mapper->getTable(), $this->sql))->set($columns);
        });
    }

    /**
     * @param string|string[] $column
     * @param mixed|int $value
     * @return int
     */
    public function increment(string|array $column, mixed $value = 1): int
    {
        return $this->transaction(function (Connection $connection) use ($column, $value) {
            if ($this->mapper->supportsTimestamp()) {
                $this->sql->addUpdateColumns([
                    $this->mapper->getTimestampColumns()[1] => date($this->manager->getDateFormat()),
                ]);
            }
            return (new Update($connection, $this->mapper->getTable(), $this->sql))->increment($column, $value);
        });
    }

    /**
     * @param string|string[] $column
     * @param mixed|int $value
     * @return int
     */
    public function decrement(string|array $column, mixed $value = 1): int
    {
        return $this->transaction(function (Connection $connection) use ($column, $value) {
            if ($this->mapper->supportsTimestamp()) {
                $this->sql->addUpdateColumns([
                    $this->mapper->getTimestampColumns()[1] => date($this->manager->getDateFormat()),
                ]);
            }
            return (new Update($connection, $this->mapper->getTable(), $this->sql))->decrement($column, $value);
        });
    }

    public function find(mixed $id): mixed
    {
        if (is_array($id)) {
            foreach ($id as $pk_column => $pk_value) {
                $this->where($pk_column)->is($pk_value);
            }
        } else {
            $this->where($this->mapper->getPrimaryKey()->columns()[0])->is($id);
        }

        return $this->get();
    }

    /**
     * @param array|string ...$ids
     * @return array
     */
    public function findAll(string|array ...$ids): array
    {
        if (is_array($ids[0])) {
            $keys = array_keys($ids[0]);
            $values = [];
            foreach ($ids as $pk_value) {
                foreach ($keys as $pk_column) {
                    $values[$pk_column][] = $pk_value[$pk_column];
                }
            }
            foreach ($values as $pk_column => $pk_values) {
                $this->where($pk_column)->in($pk_values);
            }
        } else {
            $this->where($this->mapper->getPrimaryKey()->columns()[0])->in($ids);
        }

        return $this->all();
    }

    /**
     * @param callable $callback
     * @param int $default
     * @return int
     */
    protected function transaction(callable $callback, int $default = 0): int
    {
        return $this->manager->getConnection()->transaction($callback, null, $default);
    }

    /**
     * @return EntityQuery
     */
    protected function buildQuery(): self
    {
        $this->sql->addTables([$this->mapper->getTable()]);
        return $this;
    }

    /**
     * @param array $columns
     * @return ResultSet;
     */
    protected function query(array $columns = []): ResultSet
    {
        if (!$this->buildQuery()->locked && !empty($columns)) {
            foreach ((array)$this->mapper->getPrimaryKey()->columns() as $pk_column) {
                $columns[] = $pk_column;
            }
        }

        if ($this->mapper->supportsSoftDelete()) {
            if (!$this->withSoftDeleted) {
                $this->where($this->mapper->getSoftDeleteColumn())->isNull();
            } elseif ($this->onlySoftDeleted) {
                $this->where($this->mapper->getSoftDeleteColumn())->notNull();
            }
        }

        $this->select($columns);

        $connection = $this->manager->getConnection();
        $compiler = $connection->getCompiler();

        return $connection->query($compiler->select($this->sql), $compiler->getParams());
    }

    /**
     * @return mixed
     */
    protected function executeAggregate(): mixed
    {
        $this->sql->addTables([$this->mapper->getTable()]);

        if ($this->mapper->supportsSoftDelete()) {
            if (!$this->withSoftDeleted) {
                $this->where($this->mapper->getSoftDeleteColumn())->isNull();
            } elseif ($this->onlySoftDeleted) {
                $this->where($this->mapper->getSoftDeleteColumn())->notNull();
            }
        }

        $connection = $this->manager->getConnection();
        $compiler = $connection->getCompiler();

        return $connection->column($compiler->select($this->sql), $compiler->getParams());
    }


    /**
     * @return bool
     */
    protected function isReadOnly(): bool
    {
        return !empty($this->sql->getJoins());
    }

    /**
     * @param array $results
     * @return array
     */
    protected function getLazyLoaders(array $results): array
    {
        if (empty($this->with) || empty($results)) {
            return [];
        }

        $loaders = [];
        $attr = $this->getWithAttributes();
        $relations = $this->mapper->getRelations();

        foreach ($attr['with'] as $with => $callback) {
            if (!isset($relations[$with])) {
                continue;
            }

            $loader = $relations[$with]->getLazyLoader($this->manager, $this->mapper, [
                'results' => $results,
                'callback' => $callback,
                'with' => $attr[$with]['extra'] ?? [],
                'immediate' => $this->immediate,
            ]);

            if (null === $loader) {
                continue;
            }
            $loaders[$with] = $loader;
        }

        return $loaders;
    }
}
