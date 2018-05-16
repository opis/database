---
layout: project
version: 3.6
title: Object Relational Mapper
description: Learn about Opis Database ORM
---
# ORM

1. [Introduction](#introduction)
2. [Mapping models to tables](#mapping-models-to-tables)
3. [Table's primary key](#table-s-primary-key)

## Introduction

**Opis Database ORM** provides a simple and elegant *ActiveRecord* implementation for 
working with database records, allowing you to map each table to a model. 
Models are represented by classes that inherits from `Opis\Database\Model` abstract class, 
which allow you to query tables, to insert new data in your tables, or to update or delete existing records.

Models should not directly extend the `Opis\Database\Model` class. 
Instead, users should create a class that extends the `Opis\Database\Mode`l class 
and implements all the abstract methods, then utilize that implementation as a base class for all models.

```php
use Opis\Database\Model;
use Opis\Database\Connection;

// Implementation of Opis\Database\Model
class BaseModel extends Model
{
    protected static $connection;
    
    public static function getConnection()
    {
        if(static::$connection === null)
        {
            static::$connection = new Connection('mysql:dbname=mydatabase',
                                                 'username',
                                                 'password');
        }
        
        return static::$connection;
    }
}

// User model
class User extends BaseModel
{
    // 
}
```

## Mapping models to tables

If a model's associated table name is not explicitly specified by the user, **Opis Database**
will try to automatically map that model to a table. A model named `Bar` will be mapped
to a table named `bars` and a model named `FooBar` will be mapped to a table named `foo_bars`.

You can use the `$table` property to explicitly define the name of the table associated with the model.

```php
// User model
class User extends BaseModel
{
    protected $table = 'my_users';
}
```

## Table's primary key 
{: #table-s-primary-key }

All tables are expected to have a primary key named `id`. If your table's primary key 
is named different, then you can specify the primary key's name by adding a `$primaryKey`
property to the model.

```php
// User model
class User extends BaseModel
{
    protected $primaryKey = 'uid';
}
```

By default, **Opis Database ORM** assumes that the table's primary key is an autoincrementing key. 
If you want to provide a custom primary key you must set the `$primaryKeyType` property to 
`Model::PRIMARY_KEY_CUSTOM` and overwrite the `generatePrimaryKey` method.

**Important!**{:.important}
You must make sure that the generated primary key is a unique value.
{:.well .text-primary}


```php
// User model
class User extends BaseModel
{
    protected $primaryKeyType = Model::PRIMARY_KEY_CUSTOM;
    
    protected function generatePrimaryKey()
    {
        // return a unique primary key
    }
}
```
