---
layout: project
version: 4.x
title: Creating tables
description: Learn how to create new tables
---
# Creating tables

1. [Introduction](#introduction)
2. [Adding columns](#adding-columns)
    1. [Column's size](#column-s-size)
    2. [Column's properties](#column-s-properties)
    3. [Column's constraints](#column-s-constraints)
    4. [Indexing a column](#indexing-a-column)
3. [Primary key](#primary-key)
4. [Unique keys](#unique-keys)
5. [Foreign keys](#foreign-keys)
6. [Indexes](#indexes)

## Introduction

You can create new tables by using the `create` method. This method takes two arguments:
the name of the table you want to create and a callback. The callback that receive as an argument, 
an instance of the `Opis\Database\Schema\CreateTable` class, which will be further used to add columns,
indexes and constraints to the newly created table.

```php
Opis\Database\Schema\CreateTable;

$db->schema()->create('users', function(CreateTable $table){
    //add table members
}));
```

## Adding columns
	
The schema builder provides a series of methods that allows you to add columns,
having different data types, to a table. 
The first argument that these methods take, represents the column name. 
Here is a list of supported column types and their associated methods:

##### Integer

```php
$table->integer('age');
```

Adds an `INTEGER` equivalent column

##### Float

```php
$table->float('height');
```

Adds a `FLOAT` equivalent column

##### Double

```php
$table->double('distance');
```

Adds a `DOUBLE` equivalent column

##### Decimal

```php
// Default precision
$table->decimal('ammount');

// Explicit precision
$table->decimal('ammount', 16, 4);
```

Adds a `DECIMAL` equivalent column, and optionally specify decimal's precision

##### Boolean

```php
$table->boolean('registered');
```

Adds a `BOOLEAN` equivalent column

##### Binary

```php
$table->binary('picture');
```

Adds a `BLOB` equivalent column

##### String

```php
// Default length of 255
$table->string('email');

// Explicit length
$table->string('email', 128)
```

Adds a `VARCHAR` equivalent column, and optionally specify its length

##### Fixed

```php
// Default length of 255
$table->fixed('country_code');

// Explicit length
$table->fixed('country_code', 2);
```

Adds a `CHAR` equivalent column, and optionally specify its lengths

##### Text

```php
$table->text('description');
```

Adds a `TEXT` equivalent column

##### Time

```php
$table->time('sunrise');
```

Adds a `TIME` equivalent column

##### Timestamp

```php
$table->timestamp('created_at');
```

Adds a `TIMESTAMP` equivalent column

##### Date

```php
$table->date('birthday');
```

Adds a `DATE` equivalent column

##### DateTime

```php
$table->dateTime('appointment');
```

Adds a `DATETIME` equivalent column

### Column size     
{: #column-size }

For `integer`, `text` and `binary` types you can specify the column size by calling 
the `size` method. The valid sizes values are: `tiny`, `small`, `normal`, `medium` and `big`.

```php
$db->schema()->create('users', function($table){

    $table->integer('id')->size('big');

}));
```

| Type | Size | Description |
|------|------|------------|
|integer|tiny|Adds an `TINYINT` equivalent column to the table|
|integer|small|Adds an `SMALLINT` equivalent column to the table|
|integer|normal|Adds an `INTEGER` equivalent column to the table|
|integer|medium|Adds an `MEDIUMINT` equivalent column to the table|
|integer|big|Adds an `BIGINT` equivalent column to the table|
|text|tiny|Adds an `TINYTEXT` equivalent column to the table|
|text|small|Adds an `TINYTEXT` equivalent column to the table|
|text|small|Adds an `TINYTEXT` equivalent column to the table|
|text|medium|Adds an `MEDIUMTEXT` equivalent column to the table|
|text|big|Adds an `LONGTEXT` equivalent column to the table|
|binary|tiny|Adds an `TINYBLOB` equivalent column to the table|
|binary|small|Adds an `TINYBLOB` equivalent column to the table|
|binary|normal|Adds an `BLOB` equivalent column to the table|
|binary|medium|Adds an `MEDIUMBLOB` equivalent column to the table|
|binary|big|Adds an `LONGBLOB` equivalent column to the table|
{:.table .table-align-center}

### Column properties 
{: #column-properties }

You can specify that an `integer` column contains an unsigned integer value 
by using the `unsigned` method.

```php
$table->integer('id')->unsigned();
```

You can provide a default value for column by using the `defaultValue` method.

```php
$table->string('role', 32)->defaultValue('user');
```

Specifying that a column in not nullable id bone by using the `notNull` method.

```php
$table->string('email')->notNull();
```

### Column constraints 
{: #column-constraints }

Adding a primary key constraint is done by using the `primary` method. 
The name of the constraint will be the same as the column's name.

```php
$table->integer('id')->primary();
```

Adding an unique constraint is done by using the `unique` method. 
The name of the constraint will be the same as the column's name

```php
$table->string('email')->unique();
```

For `integer` column types you can specify that the column's value will be 
incremented automatically, when a new row is inserted into the table, by using 
the `autoincrement` method. When using this feature, a `primary key` constraint 
will be automatically added to the column.

```php
$table->integer('id')->autoincrement();
```

### Indexing a column

You can specify that the current column should be indexed, by calling the `index` method.
The name of the newly added index will be the same as the column's name.

```php
$table->string('username', 32)->index();
```

## Primary key

Adding a primary key for a newly created table is done by using the `primary` method. 
This method takes as an argument the name of the column on which you want to add 
the primary key constraint. The primary key's name will be then derived from the table's name
and the name of the column on which the primary key was added.

```php
$db->schema()->create('users', function($table){

    $table->integer('id');
    // ... Add other columns
    //Add primary key
    $table->primary('id');

}));
```

**Important!**{:.important}
You can add only one primary key per table.
{:.alert.alert-warning}

If you want add a composite primary key, you simply have to pass an array of column names
to the `primary` method.

```php
$db->schema()->create('users', function($table){

    $table->integer('id');
    $table->integer('group');
    // ... Add other columns
    //Add a composite primary key on `id` and `group` columns
    $table->primary(['id', 'group']);
    
}));
```

Specifying a custom name for a primary key is also possible.

```php
$db->schema()->create('users', function($table){

    $table->integer('id');
    // ... Add other columns
    //Add a primary key named `my_custom_pk` on column `id`
    $table->primary('id', 'my_custom_pk');
    
}));
```

## Unique keys

Adding a unique key for a newly created table is done using the `unique` method. 
The name of the unique key will be derived in the same manner as the name of the primary key is.

```php
$db->schema()->create('users', function($table){

    $table->string('email');
    // ... Add other columns
    //Add unique key
    $table->unique('email');

}));
```

Composite unique keys are supported as well.

```php
$db->schema()->create('users', function($table){

    $table->string('email');
    $table->string('username');
    // ... Add other columns
    //Add a unique key on `email` and `uesername` columns
    $table->unique(['email', 'username']);

}));
```
And, of course, you can always add a custom name for a unique key.

```php
$db->schema()->create('users', function($table){

    $table->string('email');
    $table->string('username');
    // ... Add other columns
    //Add a unique key named `uk_email` on column `email`
    $table->unique('email', 'uk_email');
    //Add a unique key named `uk_composite` on `email` and `uesername` columns
    $table->unique(['email', 'username'], 'uk_composite');
    
}));
```


## Foreign keys

Adding a foreign key to a newly created table is done using the `foreign` method in
conjunction with the `references` method. The `foreign` method takes as arguments
the column on which the foreign key will be added, and the `references` method,
takes as arguments the referenced table and column.

```php
$db->schema()->create('users', function($table){

    $table->integer('profile_id');
    // ... Add other columns
    //Add a foreign key
    $table->foreign('profile_id')
          ->references('profiles', 'id');

}));
```

You may also specify options for the `on delete` and `on update` actions by using the 
`onDelete` and `onUpdate` methods. These methods accepts as an argument only the 
following string values: `cascade`, `restrict`, `no action` and `set null`.

```php
$db->schema()->create('users', function($table){

    $table->integer('profile_id');
    // ... Add other columns
    //Add a foreign key
    $table->foreign('profile_id')
          ->references('profiles', 'id')
          ->onDelete('cascade')
          ->onUpdate('cascade');

}));
```

Adding a foreign key that has a custom name can be done by passing a second argument
to the `foreign` method

```php
$db->schema()->create('users', function($table){

    $table->integer('profile_id');
    // ... Add other columns
    //Add a foreign key named `fk_custom_name`
    $table->foreign('profile_id', 'fk_custom_name')
          ->references('profiles', 'id')
          ->onDelete('cascade')
          ->onUpdate('cascade');

}));
```

## Indexes

Indexes are added by using the `index` method.

```php
$db->schema()->create('users', function($table){

    $table->string('name');
    // ... Add other columns
    //Add index
    $table->index('name');

}));
```

You can set an index on multiple columns

```php
$db->schema()->create('users', function($table){

    $table->string('name');
    $table->string('email');
    // ... Add other columns
    //Add an index on `email` and `name` columns
    $table->index(['name', 'email']);

}));
```

Adding a custom named index is simply a matter of passing a 
second argument to the `index` method.

```php
$db->schema()->create('users', function($table){

    $table->string('name');
    // ... Add other columns
    //Add an index named `idx_name` on column `name`
    $table->index('name', 'idx_name');

}));
```