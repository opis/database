---
layout: project
version: 4.x
title: Modifying tables
description: Learn how to alter existing new tables
---
# Modifying tables

1. [Alter tables](#alter-tables)
2. [Changing a column's type](#changing-a-column-s-type)
3. [Truncate tables](#truncate-tables)
4. [Rename tables](#rename-tables)
5. [Drop tables](#drop-tables)

## Alter tables

You can alter an existing table by using the `alter` method. 

```php
Opis\Database\Schema\AlterTable;

$db->schema()->alter('users', function(AlterTable $table){
    
    //code
    
}));
```

#### Adding columns

Adding a new column to an existing table is done in a similar way described in the 
[Creating tables](schema-creating-tables.html) section. 
The only difference is that you won't be able to directly add a constraint or an index.

```php
$db->schema()->alter('users', function($table){
    
    $table->integer('age')->size('small')->unsigned();
    
}));
```

#### Deleting columns

You can delete a column by using the `dropColumn` method. 
The method accepts a single argument representing the column's name.

```php
$db->schema()->alter('users', function($table){
    
    $table->dropColumn('age');
    
}));
```

#### Renaming columns

Renaming a column is done using the `renameColumn` method. 
This method takes as arguments the current and the new name of the column.

```php
$db->schema()->alter('users', function($table){
    
    $table->renameColumn('name', 'username');
    
}));
```

#### Adding default values

You can add a default value for a column by using the `setDefaultValue` method. 
This method takes as arguments the name of the column and the default value for that column.

```php
$db->schema()->alter('users', function($table){
    
    $table->setDefaultValue('age', 18);
    
}));
```

#### Removing default values

You can remove a column's default value by using the `dropDefaultValue` method. 
This method takes as an argument the name of the column.

```php
$db->schema()->alter('users', function($table){
    
    $table->dropDefaultValue('age');
    
}));
```

#### Add primary key

You may add the primary key by calling the `primary` method.

```php
$db->schema()->alter('users', function($table){
    
    $table->primary('age');
    
}));
```

#### Delete primary key

Dropping a primary key is done by using the `dropPrimary` method. 
This method takes a single argument representing the name of the primary key.

```php
$db->schema()->alter('users', function($table){
    
    $table->dropPrimary('id');
    
}));
```

#### Add unique keys

You may add a unique key by calling the `unique` method.

```php
$db->schema()->alter('users', function($table){
    
    $table->unique('email');
    
}));
```

#### Delete unique keys

Dropping a unique key is done by using the `dropUnique` method. 
This method takes a single argument representing the name of the unique key you want to delete.

```php
$db->schema()->alter('users', function($table){
    
    $table->dropUnique('email');
    
}));
```

#### Add foreign keys

You may add a foreign key by using the `foreign` method.

```php
$db->schema()->alter('users', function($table){

    $table->foreign('profile_id')->references('profiles', 'id');

}));
```

#### Delete foreign keys

Dropping a foreign key is done by using the `dropForeign` method. 
This method takes a single argument representing the name of the foreign key you want to delete.

```php
$db->schema()->alter('users', function($table){
    
    $table->dropForeign('fk_profile');
    
}));
```

#### Add indexes

You can add an index by calling the `index` method.

```php
$db->schema()->alter('users', function($table){
    
    $table->index('username');
    
}));
```

#### Delete indexes

Dropping an index is done by using the `dropIndex` method.
This method takes a single argument representing the name of the index you want to remove.

```php
$db->schema()->alter('users', function($table){
    
    $table->dropIndex('username');
    
}));
```

## Changing a column's type 
{: #changing-a-column-s-type }

When a table is altered you may change the types of its columns by using the following methods:
`toInteger`, `toFloat`, `toDouble`, `toDecimal`, `toBoolean`, `toBinary`, `toString`,
`toFixed`, `toText`, `toTime`, `toTimestamp`, `toDate`, `toDateTime`.

```php
$db->schema()->alter('users', function($table){
    $table->toDouble('height');
}));
```

## Truncate tables

You may use the `truncate` method if you want to remove all rows from a table.

```php
$db->schema()->truncate('users');
```

## Rename tables

Renaming a table is done by using the `renameTable` method. 
This method takes as arguments the current name of the table and the new name for that table.

```php
//Rename table `user` to `users` 
$db->schema()->renameTable('user', 'users');
```

## Drop tables

You may use the `drop` method if you want to delete a table.

```php
$db->schema()->drop('users');
```

