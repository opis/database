<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2015 Marius Sarca
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

namespace Opis\Database;

use RuntimeException;
use Opis\Database\ORM\Query;
use Opis\Database\ORM\Relation\HasOne;
use Opis\Database\ORM\Relation\HasMany;
use Opis\Database\ORM\Relation\BelongsTo;
use Opis\Database\ORM\Relation\BelongsToMany;

abstract class Model implements ModelInterface
{
    /**
     * Autoincrement primary key type
     *
     * @var int
     */
    const PRIMARY_KEY_AUTOINCREMENT = 1;

    /**
     * Custom primary key type
     *
     * @var int
     */
    const PRIMARY_KEY_CUSTOM = 2;

    /**
     * Model's table name
     *
     * @var string
     */
    protected $table;

    /**
     * Table's primary key
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Primary key's type
     *
     * @var int
     */
    protected $primaryKeyType = Model::PRIMARY_KEY_AUTOINCREMENT;

    /**
     * Table's associated sequence name
     *
     * @var string
     */
    protected $sequence;

    /**
     * Guarded attributes that are not mass assignable
     *
     * @var array
     */
    protected $guarded;

    /**
     * Mass asignable attributes
     *
     * @var array
     */
    protected $fillable;

    /**
     * Database instance
     *
     * @var \Opis\Database\Database
     */
    protected $database;

    /**
     * Model's short class name
     *
     * @var string
     */
    protected $className;

    /**
     * Indicates if the record is loaded
     *
     * @var boolean
     */
    protected $loaded = false;

    /**
     * Indicates if the record was deleted
     *
     * @var boolean
     */
    protected $deleted = false;

    /**
     * Indicates if the model's properties are readonly
     *
     * @var boolean
     */
    protected $readonly = false;

    /**
     * A list with related models
     *
     * @var array
     */
    protected $result = array();

    /**
     * A list with loaders
     *
     * @var array
     */
    protected $loader = array();

    /**
     * Columns' values
     *
     * @var array
     */
    protected $columns = array();

    /**
     * A list with modified columns
     *
     * @var array
     */
    protected $modified = array();

    /**
     * A list of user defined column mappings
     */
    protected $mapColumns = array();

    /**
     * Internally used column mappings
     */
    protected $mapGetSet = array();

    /**
     * A list of user defined type casts for columns
     *
     * @var array
     */
    protected $cast = array();

    /**
     * Date format
     *
     * @var string
     */
    protected $dateFormat;

    /**
     * Database connection
     *
     * @var \Opis\Database\Connection
     */
    protected $dbcon;

    /**
     * Constructor
     *
     * @final
     * @access public
     *
     * @param   boolean $readonly   Indicates if this is a read-only model
     */
    final public function __construct($readonly = false, Connection $connection = null)
    {
        $this->loaded = true;
        $this->dbcon = $connection;
        $this->readonly = $readonly;
        $this->mapGetSet = array_flip($this->mapColumns);
    }

    /**
     * Creates a new record
     *
     * @param   array                       $columns    A column-value mapped array
     * @param   \Opis\Database\Connection   $connection (optional) Database connection
     *
     * @return  \Opis\Database\Model
     */
    public static function create(array $columns, Connection $connection = null)
    {
        if ($connection === null) {
            $connection = static::getConnection();
        }

        $item = new static(false, $connection);
        $item->assign($columns);
        $item->save();
        return $item;
    }

    /**
     * Returns an instance of a model that use the given connection
     *
     * @param   \Opis\Database\Connection   $connection Database connection
     *
     * @return  \Opis\Database\Model
     */
    public static function using(Connection $connection)
    {
        return new static(false, $connection);
    }

    /**
     * Sets a columns value
     *
     * @param   string  $name   Column's name
     * @param   mixed   $value  Column's value
     */
    public function __set($name, $value)
    {
        if (!$this->loaded) {
            $this->columns[$name] = $value;
            return;
        }

        if ($this->readonly) {
            throw new RuntimeException('Readonly');
        }

        if (isset($this->mapGetSet[$name])) {
            $name = $this->mapGetSet[$name];
        }

        if ($this->primaryKey == $name) {
            return;
        }

        $mutator = $name . 'Mutator';

        if (method_exists($this, $mutator)) {
            $value = $this->{$mutator}($value);
        }

        if (isset($this->cast[$name])) {
            $value = $this->cast($name, $value);
        }

        if (method_exists($this, $name)) {
            $name = $this->{$name}()->getRelatedColumn($this, $name);
        }

        $this->modified[$name] = true;
        $this->columns[$name] = $value;
    }

    /**
     * Gets a column's value or a related model
     *
     * @param   string  $name   Key
     *
     * @return  mixed
     */
    public function __get($name)
    {
        $getter = $name;

        if (isset($this->mapGetSet[$name])) {
            $name = $this->mapGetSet[$name];
        }

        if (isset($this->columns[$name])) {
            $accesor = $getter . 'Accessor';
            $value = $this->columns[$name];

            if (isset($this->cast[$name])) {
                $value = $this->cast($name, $value);
            }

            if (method_exists($this, $accesor)) {
                return $this->{$accesor}($value);
            }

            return $value;
        }

        if (isset($this->result[$name])) {
            return $this->result[$name];
        }

        if (isset($this->loader[$name])) {
            return $this->result[$name] = $this->loader[$name]->getResult($this, $name);
        }

        if (method_exists($this, $name)) {
            return $this->result[$name] = $this->{$name}()->getResult();
        }

        throw new RuntimeException('Not found');
    }

    /**
     * Saves this model
     *
     * @return  boolean
     */
    public function save()
    {
        if ($this->deleted) {
            throw new RuntimeException('This record was deleted');
        }

        if (!isset($this->columns[$this->primaryKey])) {
            $self = $this;

            $id = $this->database()->transaction(function ($db) use ($self) {

                    $columns = $self->prepareColumns();
                    $customPK = $self->primaryKeyType === Model::PRIMARY_KEY_CUSTOM;

                    if ($customPK) {
                        $columns[$this->primaryKey] = $self->generatePrimaryKey();
                    }

                    $db->insert($columns)
                    ->into($self->getTable());

                    return $customPK ? $columns[$this->primaryKey] : $db->getConnection()->pdo()->lastInsertId($self->getSequence());
                })
                ->execute();

            $this->columns[$this->primaryKey] = $id;

            return (bool) $id;
        }

        if (!empty($this->modified)) {
            $result = $this->database()
                ->update($this->getTable())
                ->where($this->primaryKey)->is($this->columns[$this->primaryKey])
                ->set($this->prepareColumns(true));

            $this->modified = array();
            return (bool) $result;
        }

        return true;
    }

    /**
     * Deletes this model
     *
     * @return  boolean
     */
    public function delete()
    {
        if ($this->deleted) {
            throw new RuntimeException('This record was deleted');
        }

        if (!isset($this->columns[$this->primaryKey])) {
            throw new RuntimeException('This is a new record that was not saved yet');
        }

        $result = $this->database->from($this->getTable())
            ->where($this->primaryKey)->is($this->columns[$this->primaryKey])
            ->delete();
        $this->deleted = true;

        return (bool) $result;
    }

    /**
     * Mass assign values to this model
     *
     * @param   array   $values A column-value mapped array
     */
    public function assign(array $values)
    {
        if ($this->fillable !== null && is_array($this->fillable)) {
            $values = array_intersect_key($values, array_flip($this->fillable));
        } elseif ($this->guarded !== null && is_array($this->guarded)) {
            $values = array_diff_key($values, array_flip($this->guarded));
        }

        foreach ($values as $column => &$value) {
            $this->{$column} = $value;
        }
    }

    /**
     * Set a lazy loader for a property
     *
     * @param   string                          $name   Property's name
     * @param   \Opis\Database\ORM\LazyLoader   $value  Lazy loader object
     */
    public function setLazyLoader($name, $value)
    {
        $this->loader[$name] = $value;
    }

    /**
     * Get the model's associated table
     *
     * @return  string
     */
    public function getTable()
    {
        if ($this->table === null) {
            $this->table = strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $this->getClassShortName())) . 's';
        }

        return $this->table;
    }

    /**
     * Get the name of the primary key of the modle's associated table
     *
     * @return  string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Get the name of the foreign key of the modle's associated table
     *
     * @return  string
     */
    public function getForeignKey()
    {
        return str_replace('-', '_', strtolower(preg_replace('/([^A-Z])([A-Z])/', "$1_$2", $this->getClassShortName()))) . '_id';
    }

    /**
     * Define a Has One relation
     *
     * @param   string  $model          Related model
     * @param   string  $foreignKey     (optional) Foreign key
     *
     * @return  \Opis\Database\ORM\Relation\HasOne
     */
    public function hasOne($model, $foreignKey = null)
    {
        return new HasOne($this->getDatabaseConnection(), $this, new $model, $foreignKey);
    }

    /**
     * Define a Has Many relation
     *
     * @param   string  $model          Related model
     * @param   string  $foreignKey     (optional) Foreign key
     *
     * @return  \Opis\Database\ORM\Relation\HasMany
     */
    public function hasMany($model, $foreignKey = null)
    {
        return new HasMany($this->getDatabaseConnection(), $this, new $model, $foreignKey);
    }

    /**
     * Define a Belong To relation
     *
     * @param   string  $model          Related model
     * @param   string  $foreignKey     (optional) Foreign key
     *
     * @return  \Opis\Database\ORM\Relation\BelongsTo
     */
    public function belongsTo($model, $foreignKey = null)
    {
        return new BelongsTo($this->getDatabaseConnection(), $this, new $model, $foreignKey);
    }

    /**
     * Define a Many to Many relation
     *
     * @param   string  $model          Related model
     * @param   string  $foreignKey     (optional) Foreign key
     *
     * @return  \Opis\Database\ORM\Relation\BelongsToMany
     */
    public function belongsToMany($model, $foreignKey = null, $junctionTable = null, $junctionKey = null)
    {
        return new BelongsToMany($this->getDatabaseConnection(), $this, new $model, $foreignKey, $junctionTable, $junctionKey);
    }

    /**
     * Generates a unique primary key
     *
     * @return  mixed
     */
    protected function generatePrimaryKey()
    {
        throw new RuntimeException('Unimplemented method');
    }

    /**
     * Casts a column's value
     *
     * @param   string  $name   Column's name
     * @param   mixed   $value  Value to be casted
     *
     * @return  mixed
     */
    protected function cast($name, $value)
    {
        switch ($this->cast[$name]) {
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'boolean':
                return $value ? true : false;
            case 'string':
                return (string) $value;
            case 'array':
                return is_array($value) ? json_encode($value) : json_decode($value, true);
            case 'date':
                return $value instanceof DateTimeInterface ? $value : DateTime::createFromFormat($this->getDateFormat(), $value);
        }

        throw new RuntimeException(vsprintf('Unknown cast type "%s"', array($cast)));
    }

    /**
     * Get a DateTime format
     *
     * @return string
     */
    protected function getDateFormat()
    {
        if ($this->dateFormat === null) {
            $this->dateFormat = $this->database()->getConnection()->compiler()->getDateFormat();
        }

        return $this->dateFormat;
    }

    /**
     * Database instance
     *
     * @return  \Opis\Database\Database
     */
    protected function database()
    {
        if ($this->database === null) {
            $this->database = new Database($this->getDatabaseConnection());
        }

        return $this->database;
    }

    /**
     * Sequence's name
     *
     * @return  string
     */
    protected function getSequence()
    {
        if ($this->sequence === null) {
            $this->sequence = $this->getTable() . '_' . $this->primaryKey . '_seq';
        }

        return $this->sequence;
    }

    /**
     * Prepare columns
     *
     * @param   boolean $update Indicates if this is an update operation
     *
     * @return  array
     */
    protected function prepareColumns($update = false)
    {
        $results = array();

        $columns = $update ? array_intersect_key($this->columns, $this->modified) : $this->columns;

        foreach ($columns as $column => &$value) {
            if ($value instanceof Model) {
                $results[$column] = $value->{$value->primaryKey};
                continue;
            }

            $results[$column] = &$value;
        }

        return $results;
    }

    /**
     * Returns the short class name of the model
     *
     * @return  string
     */
    protected function getClassShortName()
    {
        if ($this->className === null) {
            $name = get_class($this);

            if (false !== $pos = strrpos($name, '\\')) {
                $name = substr($name, $pos + 1);
            }

            $this->className = $name;
        }

        return $this->className;
    }

    /**
     * Returns a query builder
     *
     * @return  \Opis\Database\ORM\Query
     */
    protected function queryBuilder()
    {
        return new Query($this->getDatabaseConnection(), $this);
    }

    /**
     * Returns a database connection object
     *
     * @return  \Opis\Database\Connection
     */
    protected function getDatabaseConnection()
    {
        if ($this->dbcon === null) {
            $this->dbcon = static::getConnection();
        }

        return $this->dbcon;
    }

    /**
     * Handles dynamic method calls into the model
     *
     * @param   string  $name       Method's name
     * @param   string  $arguments  Method's arguments
     *
     * @return  mixed
     */
    public function __call($name, array $arguments)
    {
        $object = $this->queryBuilder();

        if (method_exists($this, $name . 'Scope')) {
            array_unshift($arguments, $object);
            $object = $this;
            $name .= 'Scope';
        }

        return call_user_func_array(array($object, $name), $arguments);
    }

    /**
     * Handles dynamic static method calls into the model
     *
     * @param   string  $name       Method's name
     * @param   string  $arguments  Method's arguments
     *
     * @return  mixed
     */
    public static function __callStatic($name, array $arguments)
    {
        return call_user_func_array(array(new static(), $name), $arguments);
    }
}
