---
layout: project
version: 3.x
title: Aggregate functions
description: Learn about aggregate functions
canonical: /database/4.x/aggregate-functions.html
---

**Opis Database** provides support for most common and widely used aggregate functions.

## Counting

Counting records is done using the `count` method. 

```php
$count = $db->from('users')->count();

echo 'There are ' . $count . ' users registred on this site.';
```
```sql
SELECT COUNT(*) FROM `users`
```

Counting all values(`NULL` values will not be counted) of a column is done by 
passing the column's name as an argument to the `count` method. 

```php
$count = $db->from('users')->count('description');

echo 'There are ' . $count . ' users that have provided a description for their profile.';
```
```sql
SELECT COUNT(DISTINCT `country`) FROM `users`
```

## Largest value

Finding the largest value of a column is done using the `max` method. 
This method accepts the column's name as an argument. 

```php
$count = $db->from('users')->max('age');

echo 'Our oldest user is ' . $count . ' years old.';
```
```sql
SELECT MAX(`age`) FROM `users`
```

## Smallest value

Finding the smallest value of a column is done using the `min` method. 
This method accepts the column's name as an argument. 

```php
$count = $db->from('users')->min('age');

echo 'Our youngest user is ' . $count . ' years old.';
```
```sql
SELECT MIN(`age`) FROM `users`
```

## Average value

Finding the average value of a numeric column is done using the `avg` method. 
This method accepts the column's name as an argument. 

```php
$count = $db->from('users')->avg('age');

echo 'The average age of our users is ' . $count . ' years.';
```
```sql
SELECT AVG(`age`) FROM `users`
```

## Total sum

Finding the total sum of a numeric column is done using the `sum` method. 
This method accepts the column's name as an argument. 

```php
$count = $db->from('users')->sum('age');

echo 'Our users gathered together ' . $count . ' years of life experience.';
```
```sql
SELECT SUM(`age`) FROM `users`
```


