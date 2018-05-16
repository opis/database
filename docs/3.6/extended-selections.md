---
layout: project
version: 3.6
title: Extended selections
description: Learn about how to define custom selections
canonical: /database/4.x/extended-selections
---
# Extended selections

1. [Introduction](#introduction)
2. [Adding columns](#adding-columns)
3. [Adding aggregates](#adding-aggregates)
4. [Adding functions](#adding-functions)

## Introduction

Let's assume that you want to select from a table, the minimum, the maximum and 
the average value of a column. Of course, to achieve this, you could simply use 
the [aggregates functions](aggregate-functions) but, unfortunately,
this means to make three separate queries to the database. 
To overcome this issue **Opis Database** provides developers a mechanism that can 
be used to define the columns or other properties that will be included in the final result set. 

In order to use this mechanism you must pass as an argument to the `select` method 
an anonymous callback function, which in turn will receive as an argument an object 
that must be used to specify which columns should be added to the result set. 

```php
$result = $db->from('users')
             ->select(function($include){
                //code here
             })
             ->all();
```

## Adding columns

Adding columns to the result set is done using the `column` method. 

```php
$result = $db->from('users')
             ->select(function($include){
                $include->column('name');
             })
             ->all();
```
```sql
SELECT `name` FROM `users`
```

If you want to provide an alias name for the column, you must pass that alias 
as the second argument to the `column` method. 

```php


$result = $db->from('users')
             ->select(function($include){
                $include->column('name', 'n');
             })
             ->all();
```
```sql
SELECT `name` AS `n` FROM `users`
```

Of course, you can add multiple columns, by calling the `column` method multiple times, once for each column. 

```php
$result = $db->from('users')
             ->select(function($include){
                $include->column('name')
                        ->column('age');
             })
             ->all();
```
```sql
SELECT `name`, `age` FROM `users`
```

Adding multiple columns at once can be achieved by using the `columns` method 
and passing as an argument to the method an array of column names. 

```php
$result = $db->from('users')
             ->select(function($include){
                $include->columns(['name', 'age']);
             })
             ->all();
```
```sql
SELECT `name`, `age` FROM `users`
```

Aliasing column names is done by passing as an argument to the `columns` method 
a `key => value` mapped array, where the `key` represents the column name 
and the `value` represents the alias name. If you want that a column to not being aliased,
 all you have to do is to omit the `key` for that column, providing only the `value`.

```php
$result = $db->from('users')
             ->select(function($include){
                $include->columns(['user' => 'u', 'age']);
             })
             ->all();
```
```sql
SELECT `user` AS `u`, `age` FROM `users`
```

## Adding aggregates

Adding aggregates is done by using the `count`, `max`, `min`, `avg`, and `sum` methods. 
These methods can be used in conjunction with `column` and `columns` methods. 

#### Counting

Counting records is done using the count method. 

```php
$result = $db->from('users')
             ->select(function($include){
                $include->count();
             })
             ->all();
```
```sql
SELECT COUNT(*) FROM `users`
```

Counting all values(`NULL` values will not be counted) of a column is done by passing
the column's name as an argument to the `count` method. 

```php
$result = $db->from('users')
             ->select(function($include){
                $include->count('email');
             })
             ->all();
```
```sql
SELECT COUNT(`email`) FROM `users`
```

Adding an alias for the value returned by this aggregate function is done by passing
the alias name as the second argument to the `count` method.

```php
$result = $db->from('users')
             ->select(function($include){
                $include->count('email', 'email_count');
             })
             ->all();
```
```sql
SELECT COUNT(`email`) AS `email_count` FROM `users`
```

If you want to count only the distinct values of a column, you must pass `true`
 as the third argument to the `count` method. 

```php


$result = $db->from('users')
             ->select(function($include){
                $include->count('email', 'email_count', true);
             })
             ->all();
```
```sql
SELECT COUNT(DISTINCT `email`) AS `email_count` FROM `users`
```

#### Largest value

Finding the largest value of a column is done using the `max` method. 

```php
$result = $db->from('users')
             ->select(function($include){
                $include->max('age');
             })
             ->all();
```
```sql
SELECT MAX(`age`) FROM `users`
```

Adding an alias for the value returned by this aggregate function is done by passing 
the alias name as the second argument to the `max` method. 

```php
$result = $db->from('users')
             ->select(function($include){
                $include->max('age', 'max_age');
             })
             ->all();
```
```sql
SELECT MAX(`age`) AS `max_age` FROM `users`
```

#### Smallest value

Finding the smallest value of a column is done using the `min` method. 

```php
$result = $db->from('users')
             ->select(function($include){
                $include->min('age');
             })
             ->all();
```
```sql
SELECT MIN(`age`) FROM `users`
```

Adding an alias for the value returned by this aggregate function is done by passing
the alias name as the second argument to the `min` method. 

```php
$result = $db->from('users')
             ->select(function($include){
                $include->min('age', 'min_age');
             })
             ->all();
```
```sql
SELECT MIN(`age`) AS `min_age` FROM `users`
```

#### Summing

Findind the sum of a column is done using the `sum` method. 

```php
$result = $db->from('users')
             ->select(function($include){
                $include->sum('wallet');
             })
             ->all();
```
```sql
SELECT SUM(`wallet`) FROM `users`
```

Adding an alias for the value returned by this aggregate function is done by passing
the alias name as the second argument to the `sum` method.

```php
$result = $db->from('users')
             ->select(function($include){
                $include->sum('wallet', 'total_amount');
             })
             ->all();
```
```sql
SELECT SUM(`wallet`) AS `total_amount` FROM `users`
```

If you want to sum only the distinct values of a column, you must pass `true` as 
the third argument to the `sum` method. 

```php
$result = $db->from('users')
             ->select(function($include){
                $include->sum('wallet', 'total_amount', true);
             })
             ->all();
```
```sql
SELECT SUM(DISTINCT `wallet`) AS `total_amount` FROM `users`
```

#### Average

Finding the average value of a column is done using the `avg` method. 

```php
$result = $db->from('users')
             ->select(function($include){
                $include->avg('wallet');
             })
             ->all();
```
```sql
SELECT AVG(`wallet`) FROM `users`
```

Adding an alias for the value returned by this aggregate function is done by passing
the alias name as the second argument to the `avg` method. 

```php
$result = $db->from('users')
             ->select(function($include){
                $include->sum('wallet', 'average_amount');
             })
             ->all();
```
```sql
SELECT SUM(`wallet`) AS `average_amount` FROM `users`
```

## Adding functions

Adding functions is done using one of the following methods: `ucase`, `lcase`, 
`mid`, `len`, `round`, `format` and `now`.

#### Upper case

Selecting the upper case value of a column is done by using the `ucase` method.

```php
$result = $db->from('users')
             ->select(function($include){
                $include->ucase('name');
             })
             ->all();
```
```sql
SELECT UCASE(`name`) FROM `users`
```

You can also alias the returned value by passing an alias name as the second argument of the method.

```php
$result = $db->from('users')
             ->select(function($include){
                $include->ucase('ucname');
             })
             ->all();
```
```sql
SELECT UCASE(`name`) AS `ucname` FROM `users` 
```

#### Lower case

Selecting the lower case value of a column is done by using the `lcase` method.

```php
$result = $db->from('users')
             ->select(function($include){
                $include->lcase('name');
             })
             ->all();
```
```sql
SELECT LCASE(`name`) FROM `users`
```

You can also alias the returned value by passing an alias name as the second argument of the method.

```php
$result = $db->from('users')
             ->select(function($include){
                $include->lcase('lcname');
             })
             ->all();
```
```sql
SELECT LCASE(`name`) AS `lcname` FROM `users`
```

#### Substring

Extracting a substring from a column is done by using the `mid` method. 
The method takes as arguments the column's name and the starting position 
from which the substring will be extracted. The substring counting starts from `1`.

```php
$result = $db->from('users')
             ->select(function($include){
                $include->mid('name', 3);
             })
             ->all();
```
```sql
SELECT MID(`name`, 3) FROM `users`
```

You can also alias the returned value by passing an alias name as the third argument of the method.

```php
$result = $db->from('users')
             ->select(function($include){
                $include->mid('name', 3, 'sname');
             })
             ->all();
```
```sql
SELECT MID(`name`, 3) AS `sname` FROM `users`
```

You can limit the length of the substring by passing a fourth argument to the `mid` method.

```php
$result = $db->from('users')
             ->select(function($include){
                $include->mid('name', 3, null, 2);
             })
             ->all();
```
```sql
SELECT MID(`name`, 3, 2) FROM `users`
```

#### Length

Adding the length of a column is done by using the `len` method.

```php
$result = $db->from('users')
             ->select(function($include){
                $include->len('name');
             })
             ->all();
```
```sql
SELECT LENGTH(`name`) FROM `users`
```

You can also alias the returned value by passing an alias name as the second argument of the method.

```php
$result = $db->from('users')
             ->select(function($include){
                $include->len('lname');
             })
             ->all();
```
```sql
SELECT LENGTH(`name`) AS `lname` FROM `users`
```

#### Formatting

Formatting the value of a column to a number with a specified number of decimals
is done by using the `format` method.

```php
$result = $db->from('users')
             ->select(function($include){
                $include->format('wallet', 4);
             })
             ->all();
```
```sql
SELECT FORMAT(`wallet`, 4) FROM `users`
```

The same effect can be obtained by using the `round` method.

```php
$result = $db->from('users')
             ->select(function($include){
                $include->round('wallet', 4);
             })
             ->all();
```
```sql
SELECT FORMAT(`wallet`, 4) FROM `users`
```

You can also alias the returned value by passing an alias name as the second argument of the method.

```php
$result = $db->from('users')
             ->select(function($include){
                $include->format('wallet', 4, 'fwallet');
             })
             ->all();
```
```sql
SELECT FORMAT(`wallet`, 4) AS `fwallet` FROM `users`
```

