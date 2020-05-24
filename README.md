Opis Database
=============
[![Build Status](https://travis-ci.org/opis/database.png)](https://travis-ci.org/opis/database)
[![Latest Stable Version](https://poser.pugx.org/opis/database/version.png)](https://packagist.org/packages/opis/database)
[![Latest Unstable Version](https://poser.pugx.org/opis/database/v/unstable.png)](https://packagist.org/packages/opis/database)
[![License](https://poser.pugx.org/opis/database/license.png)](https://packagist.org/packages/opis/database)

Database abstraction layer
-------------------------

**Opis Database** is a library that implements an abstraction layer over the PDO extension, 
by providing a powerful query builder along with an easy to use schema builder. 
The aim of the library is to provide an unified way of interacting with databases, 
no matter of the underlying relational database management system.

Currently, we are officially supporting MySQL, PostgreSQL, Microsoft SQL, and SQLite. 
We also provide experimental support - without any commitment regarding bug fixes and updates - for Firebird, 
IBM DB2, Oracle, and NuoDB query builder.

### Documentation

The full documentation for this library can be found [here][documentation]

### License

**Opis Database** is licensed under the [Apache License, Version 2.0][apache_license]

### Requirements

* PHP 7.0.* or higher
* PDO

## Installation

**Opis Database** is available on [Packagist] and it can be installed from a 
command line interface by using [Composer]. 

```bash
composer require opis/database
```

Or you could directly reference it into your `composer.json` file as a dependency

```json
{
    "require": {
        "opis/database": "^4.0"
    }
}
```


[documentation]: https://opis.io/database
[apache_license]: https://www.apache.org/licenses/LICENSE-2.0 "Apache License"
[Packagist]: https://packagist.org/packages/opis/database "Packagist"
[Composer]: https://getcomposer.org "Composer"

