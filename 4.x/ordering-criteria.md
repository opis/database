---
layout: project
version: 4.x
title: Ordering criteria
description: Learn how to order results
---

Adding an ordering criterion is done by using the `orderBy` method.

{% capture tabs %}
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
{% capture tab_id %}{% increment tab_id %}{% endcapture %}
{% include tab.html id=tab_id title='PHP' content=php checked=true %}
{% include tab.html id=tab_id title='SQL' content=sql %}
{% endcapture %}
{% include tabs.html content=tabs %}

Changing an ordering criterion is done by passing `desc` as the second argument to the `orderBy` method.

{% capture tabs %}
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
{% capture tab_id %}{% increment tab_id %}{% endcapture %}
{% include tab.html id=tab_id title='PHP' content=php checked=true %}
{% include tab.html id=tab_id title='SQL' content=sql %}
{% endcapture %}
{% include tabs.html content=tabs %}

You can provide multiple columns as an ordering criterion, by passing to the `orderBy` method 
an array containing all column names.

{% capture tabs %}
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
{% capture tab_id %}{% increment tab_id %}{% endcapture %}
{% include tab.html id=tab_id title='PHP' content=php checked=true %}
{% include tab.html id=tab_id title='SQL' content=sql %}
{% endcapture %}
{% include tabs.html content=tabs %}


Adding multiple ordering criteria to the same query is done by calling the `orderBy` 
method as many times as you need.

{% capture tabs %}
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
{% capture tab_id %}{% increment tab_id %}{% endcapture %}
{% include tab.html id=tab_id title='PHP' content=php checked=true %}
{% include tab.html id=tab_id title='SQL' content=sql %}
{% endcapture %}
{% include tabs.html content=tabs %}

#### Ordering NULL values

You can specify how `NULL` values should be ordered by passing `nulls first` or 
`nulls last` as the third argument to the `orderBy` method.

{% capture tabs %}
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
{% capture tab_id %}{% increment tab_id %}{% endcapture %}
{% include tab.html id=tab_id title='PHP' content=php checked=true %}
{% include tab.html id=tab_id title='SQL' content=sql %}
{% endcapture %}
{% include tabs.html content=tabs %}
