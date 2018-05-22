---
layout: project
version: 3.x
title: Fetching records
description: Fetching records from a database
canonical: /database/4.x/fetching-records.html
---
# Fetching records

1. [Basic usage](#basic-usage)
2. [Distinct results](#distinct-results)
3. [Columns selection](#columns-selection)
4. [Table sources](#table-sources)

## Basic usage

Fetching records from a database is done using the `from` and the `select` methods. 

```php
$result = $db->from('users')
             ->select()
             ->all();
```

```sql
SELECT * FROM `users`
```

The result of calling the `all` method will be an array that will contain all of the records 
that were found or an empty array if no records were found. 

```php
foreach($result as $user)
{
    echo $user->name;
}
```

If you only want to retrieve a single record, you can use the `first` method. 
If no records are found the method returns `FALSE`. 

```php
$user = $db->from('users')
           ->select()
           ->first();
if($user)
{
    echo $user->name;
}
else
{
    echo 'No records were found';
}
```

Retrieving only a column's value is also possible by using the `column` method. 
If no records are found the method returns `FALSE`. 

```php
$name = $db->from('users')
           ->column('name');
           
echo $name === false ? 'No records were found' : $name;
```

**Important**{:.important}
You should always [filter](filters.html) your records
before handling the results of a query even if
you use the `first` or the `column` method, otherwise you may have performance
issues when querying over large data sets.
{:.alert.alert-warning}

## Distinct results

Retrieving only the distinct results is done using the `distinct` method. 

```php
$result = $db->from('users')
             ->distinct()
             ->select()
             ->all();
```
```sql
SELECT DISTINCT * FROM `users`
```

## Columns selection

 You can specify which columns you want to include in the result set by passing as an 
argument to the `select` method an array containing the column names. 

```php
$result = $db->from('users')
             ->select(['name' => 'n', 'email', 'age' => 'a'])
             ->all();

foreach($result as $user)
{
    echo $user->n, $user->email, $user->a;
}
```
```sql
SELECT `name` AS `n`, `email`, `age` AS `a` FROM `users`
```

## Table sources

When fetching records from a database you can specify muliple table sources by 
passing as an argument to the `from` method an array containing all table names 
you want to use. 

```php
$result = $db->from(['users', 'profiles'])
             ->select()
             ->all();
```
```sql
SELECT * FROM `users`, `profiles`
```

As in the case of columns, you can alias table names by passing as an argument 
a `key => value` mapped array, where the `key` represents the table's name and 
the `value` represents the table's alias name. If you want a table name not being 
aliased, just omit the `key` for that specific table. 

```php
$result = $db->from(['users' => 'u', 'profiles' => 'p'])
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` AS `u`, `profiles` AS `p`
```
