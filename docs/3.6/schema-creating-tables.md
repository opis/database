---
layout: project
version: 3.6
title: Creating tables
description: Learn how to create new tables
canonical: /database/4.x/schema-creating-tables
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
its first argument represents the name of the table you want to create and the second 
argument is an anonymous callback function (`Closure`) that takes as an argument 
an instance of `Opis\Database\Schema\CreateTable` class.

```php
$db->schema()->create('users', function($table){
    //add columns here
}));
```

The object passed as an argument to the anonymous callback function, will be further 
used to add columns, constraints and indexes to the newly created table.

```php
$db->schema()->create('users', function($table){
    //add column
    $table->integer('age');
}));
```

## Adding columns

Below is a list containing all columns types that you may use when building your tables.	

| Command | Description |
|---------|-------------|
|`$table->integer('age');`{:.language-php .nobg} | Adds an `INTEGER` equivalent column to the table.|
|`$table->float('height');`{:.language-php .nobg}|Adds a `FLOAT` equivalent column to the table.|
|`$table->double('distance');`{:.language-php .nobg}|Adds a `DOUBLE` equivalent column to the table.|
|`$table->decimal('ammount');`{:.language-php .nobg}|Adds a `DECIMAL` equivalent column to the table.|
|`$table->decimal('ammount', 16, 4);`{:.language-php .nobg}|Adds a `DECIMAL` equivalent column to the table and specify column's precision.|
|`$table->boolean('registered');`{:.language-php .nobg}|Adds a `BOOLEAN` equivalent column to the table.|
|`$table->binary('picture');`{:.language-php .nobg}|Adds a `BLOB` equivalent column to the table.|
|`$table->string('email');`{:.language-php .nobg}|Adds a `VARCHAR` equivalent column to the table.|
|`$table->string('email', 128);`{:.language-php .nobg}|Adds a `VARCHAR` equivalent column to the table and specify the column's length.|
|`$table->fixed('country_code');`{:.language-php .nobg}|Adds a `CHAR` equivalent column to the table.|
|`$table->fixed('country_code', 2);`{:.language-php .nobg}|Adds a `CHAR` equivalent column to the table and specify the column's length.|
|`$table->text('description');`{:.language-php .nobg}|Adds a `TEXT` equivalent column to the table.|
|`$table->time('sunrise');`{:.language-php .nobg}|Adds a `TIME` equivalent column to the table.|
|`$table->timestamp('created_at');`{:.language-php .nobg}|Adds a `TIMESTAMP` equivalent column to the table.|
|`$table->string('email');`{:.language-php .nobg}|Adds a `VARCHAR` equivalent column to the table.|
|`$table->date('created_at');`{:.language-php .nobg}|Adds a `DATE` equivalent column to the table.|
|`$table->dateTime('created_at');`{:.language-php .nobg}|Adds a `DATETIME` equivalent column to the table.|
{:.table .table-bordered .box-highlight}

#### Column's size 
{: #column-s-size }

For `integer`, `text` and `binary` types you can specify the column size by calling 
the `size` method. The valid sizes values are: `tiny`, `small`, `normal`, `medium` and `big`.

```php
$db->schema()->create('users', function($table){

    $table->integer('id')->size('big');

}));
```

| Command | Description |
|---------|-------------|
|`$table->integer('column')->size('tiny');`{:.language-php .nobg}|Adds an `TINYINT` equivalent column to the table.|
|`$table->integer('column')->size('small');`{:.language-php .nobg}|Adds an `SMALLINT` equivalent column to the table.|
|`$table->integer('column')->size('normal');`{:.language-php .nobg}|Adds an `INTEGER` equivalent column to the table.|
|`$table->integer('column')->size('medium');`{:.language-php .nobg}|Adds an MEDIUMINT equivalent column to the table.|
|`$table->integer('column')->size('big');`{:.language-php .nobg}|Adds an BIGINT equivalent column to the table.|
|`$table->text('column')->size('tiny');`{:.language-php .nobg}|Adds an `TINYTEXT` equivalent column to the table.|
|`$table->text('column')->size('small');`{:.language-php .nobg}|Adds an `TINYTEXT` equivalent column to the table.|
|`$table->text('column')->size('normal');`{:.language-php .nobg}|Adds an `TEXT` equivalent column to the table.|
|`$table->text('column')->size('medium');`{:.language-php .nobg}|Adds an `MEDIUMTEXT` equivalent column to the table.|
|`$table->text('column')->size('big');`{:.language-php .nobg}|Adds an `LONGTEXT` equivalent column to the table.|
|`$table->binary('column')->size('tiny');`{:.language-php .nobg}|Adds an `TINYBLOB` equivalent column to the table.|
|`$table->binary('column')->size('small');`{:.language-php .nobg}|Adds an `TINYBLOB` equivalent column to the table.|
|`$table->binary('column')->size('normal');`{:.language-php .nobg}|Adds an `BLOB` equivalent column to the table.|
|`$table->binary('column')->size('medium');`{:.language-php .nobg}|Adds an `MEDIUMBLOB` equivalent column to the table.|
|`$table->binary('column')->size('big');`{:.language-php .nobg}|Adds an `LONGBLOB` equivalent column to the table.|
{:.table .table-bordered .box-highlight}

#### Column's properties 
{: #column-s-properties }

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

#### Column's constraints 
{: #column-s-constraints }

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

#### Indexing a column

You can specify that the current column should be indexed, by calling the `index` method.
The name of the newly added index will be the same as the column's name.

```php
$table->string('username', 32)->index();
```

## Primary key

Adding a primary key for a newly created table is done using the `primary` method. 
The method takes as an argument the name of the column on which you want to add 
the primary key constraint. The name of the primary key will be the same as the column's name.

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
{:.well .text-primary}

Adding a primary key that has a custom name can be done by passing as arguments 
to the `primary` method the name of the primary key and the name of the column on 
which the primary key will be added.

```php
$db->schema()->create('users', function($table){

    $table->integer('id');
    // ... Add other columns
    //Add a primary key named `pk_id` on column `id`
    $table->primary('pk_id', 'id');

}));
```

If you want add a primary key on multiple columns, you simply have to pass as arguments 
to the `primary` method the name of the primary key and an array containing the column names 
on which the primary key will be added.

```php
$db->schema()->create('users', function($table){

    $table->integer('id');
    $table->integer('group');
    // ... Add other columns
    //Add a primary key named `users_pk` on columns `id` and `group`
    $table->primary('users_pk', array('id', 'group'));

}));
```

## Unique keys

Adding a unique key for a newly created table is done using the `unique` method. 
The method takes as an argument the name of the column on which you want to add 
the unique key constraint. The name of the unique key will be the same as the column's name.

```php
$db->schema()->create('users', function($table){

    $table->string('email');
    // ... Add other columns
    //Add unique key
    $table->unique('email');

}));
```

Adding a unique key that has a custom name can be done by passing as arguments to 
the `unique` method the name of the unique key and the name of the column on 
which the unique key will be added.

```php
$db->schema()->create('users', function($table){

    $table->string('email');
    // ... Add other columns
    //Add a unique key named `uk_email` on column `email`
    $table->unique('uk_email', 'email');

}));
```

If you want add a unique key on multiple columns, you simply have to pass as arguments
to the `unique` method the name of the unique key and an array containing the column names 
on which the unique key will be added.

```php
$db->schema()->create('users', function($table){

    $table->string('email');
    $table->string('username');
    // ... Add other columns
    //Add a unique key named `uk_users` on columns `email` and `uesername`
    $table->unique('uk_users', array('email', 'username'));

}));
```

## Foreign keys

Adding a foreign key to a newly created table is done using the `foreign` method. 
The method takes as an argument the name of the column on which you want to add the foreign key. 
The name of the foreign will be the same as the column's name.

The referenced table is set by calling the `references` method and the referenced column 
is set by calling the `on` method.

```php
$db->schema()->create('users', function($table){

    $table->integer('profile_id');
    // ... Add other columns
    //Add a foreign key
    $table->foreign('profile_id')->references('profiles')->on('id');

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
          ->references('profiles')->on('id')
          ->onDelete('cascade')
          ->onUpdate('cascade');

}));
```

Adding a foreign key that has a custom name can be done by passing as arguments to the 
`foreign` method the name of the foreign key and the name of the column on which the 
foreign key will be added.

```php
$db->schema()->create('users', function($table){

    $table->integer('profile_id');
    // ... Add other columns
    //Add a foreign key named `fk_custom_name` on column `profile_id`
    $table->foreign('fk_custom_name', 'profile_id')->references('profiles')->on('id');

}));
```

## Indexes

Adding an index for a newly created table is done using the `index` method. 
The method takes as an argument the name of the column on which you want to add the index. 
The name of the index will be the same as the column's name.

```php
$db->schema()->create('users', function($table){

    $table->string('name');
    // ... Add other columns
    //Add index
    $table->index('name');

}));
```

Adding a index that has a custom name can be done by passing as arguments 
to the `index` method the name of the index and the name of the column that you want to be indexed.

```php
$db->schema()->create('users', function($table){

    $table->string('name');
    // ... Add other columns
    //Add an index named `idx_name` on column `name`
    $table->index('idx_name', 'name');

}));
```

If you want add a index on multiple columns, you simply have to pass as arguments 
to the `index` method the name of the index and an array containing the column names 
on which the index will be added.

```php
$db->schema()->create('users', function($table){

    $table->string('name');
    $table->string('email');
    // ... Add other columns
    //Add an index named `idx_users` on columns `email` and `name`
    $table->index('idx_users', array('email', 'name'));

}));
```
