---
layout: project
version: 3.x
title: Filters
description: Learn how to filter records
canonical: /database/4.x/filters.html
---

Filtering records is one of the most important and intensively used operation in SQL. 
**Opis Database** aims to make data filtering an easy task for the developers, 
by implementing a set of targeted methods capable of simplifying the filtering process.

## Adding filters

Adding a filtering condition is done by using the `where` method in conjunction 
with one of the following methods: `is` or `eq`, `isNot` or `ne`, `lessThan` or `lt`,
`greaterThan` or `gt`, `atLeast` or `gte`, `atMost` or `lte`, `between`, `notBetween`, 
`in`, `notIn`, `like`, `notLike`, `isNull` and `notNull`.

#### The *is* method {#the-is-method}

Adds a filtering condition, so that only those records, that have the specified 
column's value equal to a given value, will be added to the result set. 
Alternatively, to add this filtering condition, you can use the `eq`(equal) method, 
which is an alias of the `is` method.

```php
// Select all users that are 18.

$result = $db->from('users')
             ->where('age')->is(18) //Alternatively: ->where('age')->eq(18)
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `age` = 18
```

#### The *isNot* method {#the-isnot-method}

Adds a filtering condition, so that only those records, that have the specified 
column's value not equal to a given value, will be added to the result set. 
Alternatively, to add this filtering condition, you can use the `ne`(not equal) method,
 which is an alias of the `isNot` method.

```php
// Select all users that are not 18.

$result = $db->from('users')
             ->where('age')->isNot(18) //Alternatively: ->where('age')->ne(18)
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `age` != 18
```

#### The *lessThan* method {#the-lessthan-method}

Adds a filtering condition, so that only those records, that have the specified 
column's value lesser than a given value, will be added to the result set. 
Alternatively, to add this filtering condition, you can use the `lt`(less than) method, 
which is an alias of the `lessThan` method.

```php
// Select all users that are under 18.

$result = $db->from('users')
             ->where('age')->lessThan(18) //Alternatively: ->where('age')->lt(18)
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `age` < 18
```

#### The *greaterThan* method {#the-greaterthan-method}

Adds a filtering condition, so that only those, records that have the specified column's 
value greater than a given value, will be added to the result set. 
Alternatively, to add this filtering condition, you can use the `gt`(greater than) method,
 which is an alias of the `greaterThan` method.

```php
// Select all users that are over 18.

$result = $db->from('users')
             ->where('age')->greaterThan(18) //Alternatively: ->where('age')->gt(18)
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `age` > 18
```

#### The *atLeast* method {#the-atleast-method}

Adds a filtering condition, so that only those records, that have the specified 
column's value greater than or equal to a given value, will be added to the result set. 
Alternatively, to add this filtering condition, you can use the `gte`(greater than or equal) method,
 which is an alias of the `atLeast` method.

```php
// Select all users that are at least 18.

$result = $db->from('users')
             ->where('age')->atLeast(18) //Alternatively: ->where('age')->gte(18)
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `age` >= 18
```

#### The *atMost* method {#the-atmost-method}

Adds a filtering condition, so that only those records, that have the specified 
column's value lesser than or equal to a given value, will be added to the result set.
 Alternatively, to add this filtering condition, you can use the `lte`(less than or equal) method,
 which is an alias of the `atMost` method.

```php
// Select all users that are at most 18.

$result = $db->from('users')
             ->where('age')->atMost(18) //Alternatively: ->where('age')->lte(18)
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `age` <= 18
```

#### The *between* method {#the-between-method}

Adds a filtering condition, so that only those records, that have the specified 
column's value within a given range, will be added to the result set.

```php
// Select all users that are between 18 and 21.

$result = $db->from('users')
             ->where('age')->between(18, 21)
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `age` BETWEEN 18 AND 21
```

#### The *notBetween* method {#the-notbetween-method}

Adds a filtering condition, so that only those records, that don't have the specified column's 
value within a given range, will be added to the result set.

```php
// Select all users that are not between 18 and 21.

$result = $db->from('users')
             ->where('age')->notBetween(18, 21)
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `age` NOT BETWEEN 18 AND 21
```

#### The *in* method {#the-in-method}

Adds a filtering condition, so that only those records, that have the specified 
column's value contained within a given set of values, will be added to the result set.

```php
// Select all users that are living in London, New York or Paris.

$result = $db->from('users')
             ->where('city')->in(['London', 'New York', 'Paris'])
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `city` IN ("London", "New York", "Paris")
```

Instead of providing a set of values by passing an array to the `in` method, you could also 
obtain a set of values by using a subquery. To use a subquery, just pass an anonymous function callback
as an argument to the `in` method, then use the object that will be passed as an argument to your 
callback function to build your query.

```php
/**
 * Select all users that are living in a city
 * which has a population of over 10 millions inhabitants.
 */
 
$result = $db->from('users')
             ->where('city')->in(function($query){
                $query->from('cities')
                      ->where('population')->atLeast(10000000)
                      ->select('name');
             })
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` 
WHERE `city` IN (SELECT `name` FROM `cities` WHERE `population` >= 10000000)
```

#### The *notIn* method {#the-notin-method}

Adds a filtering condition, so that only those records, that don't have the specified column's
value contained within a given set of values, will be added to the result set.

```php
// Select all users that are not living in London, New York or Paris.

$result = $db->from('users')
             ->where('city')->notIn(['London', 'New York', 'Paris'])
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `city` NOT IN ("London", "New York", "Paris")
```

As in the case of the `in` method, you could obtain a set of values to be used for comparison,
by using a subquery.

```php
/**
 * Select all users that are not living in a city
 * which has a population of over 10 millions inhabitants.
 */
 
$result = $db->from('users')
             ->where('city')->notIn(function($query){
                $query->from('cities')
                      ->where('population')->atLeast(10000000)
                      ->select('name');
             })
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` 
WHERE `city` NOT IN (SELECT `name` FROM `cities` WHERE `population` >= 10000000)
```

#### The *like* method {#the-like-method}

Adds a filtering condition, so that only those records, whose specified column's 
value match a given pattern, will be added to the result set.

```php
/**
 * Select all users that are living in a city
 * whose name starts with the letter 'P'.
 */

$result = $db->from('users')
             ->where('city')->like('P%')
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `city` LIKE "P%"
```

#### The *notLike* method {#the-notlike-method}

Adds a filtering condition, so that only those records, whose specified column's 
value don't match a given pattern, will be added to the result set.

```php
/**
 * Select all users that are living in a city
 * whose name doesn't starts with the letter 'P'.
 */
 
$result = $db->from('users')
             ->where('city')->notLike('P%')
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `city` NOT LIKE "P%"
```

#### The *isNull* method {#the-isnull-method}

Adds a filtering condition, so that only those records, that have the specified column's
value equal to `NULL`, will be added to the result set.

```php
// Select all users that do not have a website.
 
$result = $db->from('users')
             ->where('website')->isNull()
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `website` IS NULL
```

#### The *notNull* method {#the-notnull-method}

Adds a filtering condition, so that only those records, that have the specified 
column's value not equal to `NULL`, will be added to the result set.

```php
// Select all users that do have a website.
 
$result = $db->from('users')
             ->where('website')->notNull()
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `website` IS NOT NULL
```
 
## Multiple conditions

Adding multiple conditions to your query is done by using the `andWhere` and `orWhere` methods. 
Those methods works exactly as the `where` method and depending on which method you use, 
they will combine with the previous declared condition by using an `AND` or an `OR` operator.

To add an additional condition to your query, that combines with the previous declared condition by 
using an `AND` operator, use the `andWhere` method.

```php
// Select all users that are 18 and are living in London.
 
$result = $db->from('users')
             ->where('age')->is(18)
             ->andWhere('city')->is('London')
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `age` = 18 AND `city` = "London"
```

To add an additional condition to your query, that combines with the previous declared condition 
by using an `OR` operator, use the `orWhere` method.

```php
// Select all users that are either 18 or 21.
 
$result = $db->from('users')
             ->where('age')->is(18)
             ->orWhere('age')->is(21)
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `age` = 18 OR `age` = 21
```

You can group your conditions in order to add a more complex filter to your query. 
Grouping conditions is done by passing as an argument to the `where`, `andWhere` or the `orWhere`
methods an anonymous callback function. The callback functions takes a single argument 
that will be further used to add filtering conditions to your query.

```php
/**
 * Select all users that are 18 and
 * are living either in London or Paris.
 */
 
$result = $db->from('users')
             ->where('age')->is(18)
             ->andWhere(function($group){
                $group->where('city')->is('London')
                      ->orWhere('city')->is('Paris');
             })
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `age` = 18 AND (`city` = "London" OR `city` = "Paris")
```

## Columns comparison

**Opis Database** allows you to add comparison conditions between columns.
You can add comparison conditions between two columns, by passing `TRUE` as a second argument 
to the `is`, `isNot`, `lessThan`, `greaterThan`, `atLeast` and `atMost` methods, or to their 
corresponding aliases `eq` , `ne`, `lt`, `gt`, `lte` and `gte` methods.

```php
/**
 * Select all users that are living in the same city they were born.
 */
 
$result = $db->from('users')
             ->where('city')->eq('birthplace', true)
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `city` = `birthplace`
```

Building the above query without passing `TRUE` as the second argument to the `eq` method, 
will result into a query that will select all users that are living in a city named `birthplace`.

```php
$result = $db->from('users')
             ->where('city')->eq('birthplace')
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` WHERE `city` = "birthplace"
```

## The EXISTS condition

This condition is used in combination with a subquery and is considered to be met, 
if the subquery returns at least one row. Adding an `EXISTS` condition is done by using the 
`whereExists` and the `whereNotExists` methods. You can add multiple `EXISTS` conditions by using 
the `andWhereExists` or `andWhereNotExists` methods and the `orWhereExists` or `orWhereNotExists` methods.

These methods are used in a similar manner as the `where`, `andWhere` and `orWhere` methods, 
receiving as an argument an anonymous function callback, that will be further used to build a subquery.

```php
/**
 * Select all users that had purchased at least one product.
 */
 
$result = $db->from('users')
             ->whereExists(function($query){
                $query->from('orders')
                      ->where('orders.name')->eq('users.name', true)
                      ->select();
             })
             ->select()
             ->all();
```
```sql
SELECT * FROM `users` 
WHERE EXISTS (SELECT * FROM `orders` WHERE `orders`.`name` = `users`.`name`)
```

