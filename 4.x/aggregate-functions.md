---
layout: project
version: 4.x
title: Aggregate functions
description: A list of aggregate functions that you can use when building your query
keywords: aggregate, functions, count, average, sum, total
---

**Opis Database** provides support for most common and widely used aggregate functions.

## Counting

Counting records is done using the `count` method. 


{% capture php %}
```php
$count = $db->from('users')->count();

echo 'There are ' . $count . ' users registred on this site.';
```
{% endcapture %}
{% capture sql%}
```sql
SELECT COUNT(*) FROM `users`
```
{% endcapture %}
{% include_relative _tabs.html %}


Counting all values(`NULL` values will not be counted) of a column is done by 
passing the column's name as an argument to the `count` method. 


{% capture php %}
```php
$count = $db->from('users')->count('description');

echo 'There are ' . $count . ' users that have provided a description for their profile.';
```
{% endcapture %}
{% capture sql%}
```sql
SELECT COUNT(DISTINCT `country`) FROM `users`
```
{% endcapture %}
{% include_relative _tabs.html %}

## Largest value

Finding the largest value of a column is done using the `max` method. 
This method accepts the column's name as an argument. 


{% capture php %}
```php
$count = $db->from('users')->max('age');

echo 'Our oldest user is ' . $count . ' years old.';
```
{% endcapture %}
{% capture sql%}
```sql
SELECT MAX(`age`) FROM `users`
```
{% endcapture %}
{% include_relative _tabs.html %}

## Smallest value

Finding the smallest value of a column is done using the `min` method. 
This method accepts the column's name as an argument. 


{% capture php %}
```php
$count = $db->from('users')->min('age');

echo 'Our youngest user is ' . $count . ' years old.';
```
{% endcapture %}
{% capture sql%}
```sql
SELECT MIN(`age`) FROM `users`
```
{% endcapture %}
{% include_relative _tabs.html %}

## Average value

Finding the average value of a numeric column is done using the `avg` method. 
This method accepts the column's name as an argument. 


{% capture php %}
```php
$count = $db->from('users')->avg('age');

echo 'The average age of our users is ' . $count . ' years.';
```
{% endcapture %}
{% capture sql%}
```sql
SELECT AVG(`age`) FROM `users`
```
{% endcapture %}
{% include_relative _tabs.html %}


## Total sum

Finding the total sum of a numeric column is done using the `sum` method. 
This method accepts the column's name as an argument. 


{% capture php %}
```php
$count = $db->from('users')->sum('age');

echo 'Our users gathered together ' . $count . ' years of life experience.';
```
{% endcapture %}
{% capture sql%}
```sql
SELECT SUM(`age`) FROM `users`
```
{% endcapture %}
{% include_relative _tabs.html %}
