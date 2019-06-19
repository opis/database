---
layout: project
version: 3.x
title: Joins
description: Learn how to perform joins
canonical: /database/4.x/joins.html
---

Joins are used to combine rows from two or more tables, based on a common field between them.
**Opis Database** allows you to perform four type of joins: **INNER** join by using the `join` method,
**LEFT** join by using the `leftJoin` method, **RIGHT** join by using the `rightJoin` method 
and **FULL** join by using the `fullJoin` method.

Performing a join is done in a similar manner for all four types of joins. 
All join methods accepts two arguments: the first argument represents the joined table 
and the second argument must be an anonymous callback function, which will receive as an argument
an object that will be further used to set the join's conditions to be met.

Adding a join condition is done by using the `on` method. This methods accepts 
two arguments, representing the two columns on which the join will be performed, 
and optionally a third argument representing the comparison operator which 
can be one of the following: `=`, `!=`, `>`, `<`, `>=` and `<=`. 
If no comparison operator is specified, the `=` operator will be used by default.

```php
$result = $db->from('users')
             ->join('profiles', function($join){
                $join->on('users.id', 'profiles.id');
             })
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` INNER JOIN `profiles` ON `users`.`id` = `profiles`.`id`
```

Adding multiple join conditions is done by using the `andOn` and `orOn` method. 
Depending on which method you use, the join condition will be combined with the 
previous declared join condition using the `AND` or the `OR` operator.

To add an additional condition to your join expression, that combines with 
the previous declared condition by using an `AND` operator, use the `andOn` method.

```php
$result = $db->from('users')
             ->join('profiles', function($join){
                $join->on('users.id', 'profiles.id')
                     ->andOn('users.email', 'profile.primary_email');
             })
             ->select()
             ->all();
```
```sql
SELECT * FROM `users`
    INNER JOIN `profiles`
        ON `users`.`id` = `profiles`.`id`
        AND `users`.`email` = `profile`.`primary_email`
```

To add an additional condition to your join expression, that combines with the previous 
declared condition by using an `OR` operator, use the `orOn` method.

```php
$result = $db->from('users')
             ->join('profiles', function($join){
                $join->on('users.id', 'profiles.id')
                     ->orOn('users.email', 'profile.primary_email');
             })
             ->select()
             ->all();
```
```sql
SELECT * FROM `users`
    INNER JOIN `profiles`
        ON `users`.`id` = `profiles`.`id`
        OR `users`.`email` = `profile`.`primary_email`
```

You can also group your join conditions, by passing as an argument to the 
`on`, `andOn` and `orOn` methods, an anonymous callback function.

```php
$result = $db->from('users')
             ->join('profiles', function($join){
                $join->on('users.id', 'profiles.id')
                     ->andOn(function($join){
                        $join->on('users.email', 'profiles.primary_email')
                             ->orOn('users.email', 'profiles.secondary_email');
                     });
             })
             ->select()
             ->all();
```
```sql
SELECT * FROM `users`
    INNER JOIN `profiles`
        ON `users`.`id` = `profiles`.`id`
        AND (`users`.`email` = `profiles`.`primary_email`
                OR
             `users`.`email` = `profiles`.`secondary_email`)
```

Aliasing the table name used within a join, is done by passing a `key => value` 
mapped array to the used join method, where the `key` represents the table's name 
and the `value` represents the alias name.

```php
$result = $db->from('users')
             ->join(['profiles' => 'p'], function($join){
                $join->on('users.id', 'p.id');
             })
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` INNER JOIN `profiles` AS `p` ON `users`.`id` = `p`.`id`
```

