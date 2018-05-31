---
layout: project
version: 4.x
title: About
description: A database abstraction layer over PDO, 
    that provides a powerful and intuitive query builder, bundled with an easy to use schema builder
keywords: DAL, database, sql, query builder, schema builder, abstraction layer
lib: 
    name: opis/database
    version: 4.0.0
---
# About

**Opis Database** is a library that implements an abstraction layer over the PDO extension, 
by providing a powerful query builder along with an easy to use schema builder.
The aim of the library is to provide an unified way of interacting with databases,
no matter of the underlying relational database management system. 

Currently, we are officially supporting MySQL, PostgreSQL, Microsoft SQL, and SQLite.
We also provide experimental support - without any commitment regarding bug fixes and updates - 
for Firebird, IBM DB2, Oracle, and NuoDB query builder.

## License

**Opis Database** is licensed under [Apache License, Version 2.0][apache_license].

## Requirements

* PHP 7.0 or higher
* PDO

## Installation

**Opis Database** is available on [Packagist] and it can be installed from a 
command line interface by using [Composer]. 

```bash
composer require {{page.lib.name}}
```

Or you could directly reference it into your `composer.json` file as a dependency

```json
{
    "require": {
        "{{page.lib.name}}": "^{{page.lib.version}}"
    }
}
```


[apache_license]: http://www.apache.org/licenses/LICENSE-2.0 "Project license" 
{:rel="nofollow" target="_blank"}
[Packagist]: https://packagist.org/packages/{{page.lib.name}} "Packagist" 
{:rel="nofollow" target="_blank"}
[Composer]: http://getcomposer.org "Composer" 
{:ref="nofollow" target="_blank"}
