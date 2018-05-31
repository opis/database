---
layout: project
version: 4.x
title: Database connections
description: Define and establish a connection to a database. 
 Learn how you can configure your connection settings
keywords: connection, settings, DSN
---

# Database connections

1. [Introduction](#introduction)
2. [Connection options](#connection-options)

## Introduction

Interaction with a database is achieved with the help of the `Opis\Database\Database` class, 
which provides various methods that will ease the process of manipulating tables and records. 
The constructor of this class takes as an argument an instance of `Opis\Database\Connection`.
 
The `Connection` class is responsible both for establishing a connection to the database server, 
as well as for sending and receiving data. 
The constructor of the `Connection` class accepts parameters for specifying
the [DSN] and optionally for the username and password(if any). 

```php
use Opis\Database\Database;
use Opis\Database\Connection;

$connection = new Connection('mysql:host=localhost;dbname=test', 'username', 'password');

$db = new Database($connection);
```

You can also create a connection by using the `fromPDO` static method and 
passing an instance of the `PDO` class as an argument to the method. 

```php
$connection = Connection::fromPDO($pdo);
```

## Connection options

The [DSN], the username and the password provided when instantiating a new
`Opis\Database\Connection` class, will be further used to build a `PDO` object that will actually
 establish a connection to the database. 
**Opis Database** allows you to specify options for the `PDO` object by calling the `option` method. 

```php
$connection = Connection::create($dsn, $user, $password)
                        ->option(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ)
                        ->option(PDO::ATTR_STRINGIFY_FETCHES, false);
```

Setting multiple options simultaneously is done by calling the `options` method 
and passing as an argument an array of options.

```php
$connection = Connection::create($dsn, $user, $password)
                        ->options([
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                            PDO::ATTR_STRINGIFY_FETCHES => false
                        ]);
```

Making a connection persistent is done by using the `persistent` method. 

```php
$connection = Connection::create($dsn, $user, $password)
                        ->persistent();
```

You can keep a log with all of the queries sent to a database by calling the `logQueries` method. 
Retrieving the list of logged queries is done using the `getLog` method. 

```php
$connection = Connection::create($dsn, $user, $password)
                        ->logQueries();

//Your queries...

foreach($connection->getLog() as $entry)
{
    echo $entry;
}
```

You also have the possibility to specify a list of commands that will be executed after connecting
to a database by using the `initCommand` method. 

```php
$connection = Connection::create($dsn, $user, $password)
                        ->initCommand('SET NAMES UTF-8');
```


[DSN]: http://en.wikipedia.org/wiki/Data_source_name "Data source name" 
{:rel="nofollow" target="_blank" data-toggle="tooltip"}