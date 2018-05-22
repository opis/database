---
layout: project
version: 4.x
title: Schema
description: Learn about Opis Database schema
---
# Schema

1. [Introduction](#introduction)
2. [Table information](#table-information)
3. [Getting the columns of a table](#getting-the-columns-of-a-table)

**Important!**
At this moment all the features presented here are available only for
**MySQL**, **SQLServer**, **PostgreSQL** and **SQLite** database systems.
{:.alert.alert-info}

## Introduction

**Opis Database** provides an unified **API** across all database supported systems 
that allows developers to manipulate tables or to obtain other informations about a database.

Accessing the database associated schema is done by calling the `schema` method.

```php
use Opis\Database\Database;
use Opis\Database\Connection;

$connection = new Connection('mysql:host=localhost;dbname=test', 'username', 'password');

$db = new Database($connection);

$schema = $db->schema();
```

## Table information

Obtaining a list of tables from the current database is done by using the `getTables` method.

```php
$tables = $db->schema()->getTables();

foreach($tables as $table)
{
    //do something
}
```

By default the list of tables obtained by calling this method is cached for performance reasons. 
If you want to obtain an uncached list of tables then pass `true` as an argument to the `getTables` method.

```php
$tables = $db->schema()->getTables(true);

foreach($tables as $table)
{
    //do something
}
```

You can check if a specific table exists by passing the table name 
as the first argument to the `hasTable` method.

```php
if($db->schema()->hasTable('users'));
{
    //do something
}
```

This method accepts as an optional second argument a boolean value that indicates 
if the checking for the table's existence should be done by using an uncached list of tables.
The default value of this optional argument is `false`.

```php
if($db->schema()->hasTable('users', true));
{
    //do something
}
```

## Getting the columns of a table

Getting a table's columns list is done by using the `getColumns` method. 
The method takes as an argument the name of the table.

```php
$columns = $db->schema()->getColumns('users');

foreach($columns as $column)
{
    //do something
}
```

If you want to obtain an uncached list of columns then pass `true` as an argument 
to the `getColumns` method.

```php
$columns = $db->schema()->getColumns('users', true);

foreach($columns as $column)
{
    //do something
}
```
