---
layout: project
version: 4.x
title: Ordering criteria
description: Learn how to order results
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
{% capture sql%}
```sql
SELECT * FROM `users` ORDER BY `name` ASC
```
{% endcapture %}
{% include_relative _tabs.html %}

Changing an ordering criterion is done by passing `desc` as the second argument to the `orderBy` method.


{% capture php %}
```php
$result = $db->from('users')
             ->orderBy('name', 'desc')
             ->select()
             ->all();
```
{% endcapture %}
{% capture sql%}
```sql
SELECT * FROM `users` ORDER BY `name` DESC
```
{% endcapture %}
{% include_relative _tabs.html %}

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
{% capture sql%}
```sql
SELECT * FROM `users` ORDER BY `name`, `age` ASC
```
{% endcapture %}
{% include_relative _tabs.html %}


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
{% capture sql%}
```sql
SELECT * FROM `users` ORDER BY `name` ASC, `age` DESC
```
{% endcapture %}
{% include_relative _tabs.html %}

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
{% capture sql%}
```sql
SELECT * FROM `users` ORDER BY `name` ASC, `age` DESC NULLS FIRST
```
{% endcapture %}
{% include_relative _tabs.html %}
