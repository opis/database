---
layout: project
version: 3.x
title: Ordering criteria
description: Learn how to order results
canonical: /database/4.x/ordering-criteria.html
---

Adding an ordering criterion is done by using the `orderBy` method.

{% capture php %}
```php
$result = $db->from('users')
             ->orderBy('name')
             ->select()
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT * FROM `users` ORDER BY `name` ASC
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

Changing an ordering criterion is done by passing `desc` as the second argument to the `orderBy` method.

{% capture php %}
```php
$result = $db->from('users')
             ->orderBy('name', 'desc')
             ->select()
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT * FROM `users` ORDER BY `name` DESC
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

You can provide multiple columns as an ordering criterion, by passing to the `orderBy` method 
an array containing all column names.

{% capture php %}
```php
$result = $db->from('users')
             ->orderBy(['name', 'age'])
             ->select()
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT * FROM `users` ORDER BY `name`, `age` ASC
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

Adding multiple ordering criteria to the same query is done by calling the `orderBy` 
method as many times as you need.

{% capture php %}
```php
$result = $db->from('users')
             ->orderBy('name')
             ->orderBy('age', 'desc')
             ->select()
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT * FROM `users` ORDER BY `name` ASC, `age` DESC
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

#### Ordering NULL values

You can specify how `NULL` values should be ordered by passing `nulls first` or 
`nulls last` as the third argument to the `orderBy` method.

{% capture php %}
```php
$result = $db->from('users')
             ->orderBy('name')
             ->orderBy('age', 'desc', 'nulls first')
             ->select()
             ->all();
```
{% endcapture %}
{% capture sql %}
```sql
SELECT * FROM `users` ORDER BY `name` ASC, `age` DESC NULLS FIRST
```
{% endcapture %}
{% include tabs.html 1="PHP" 2="SQL" _1=php _2=sql %}

