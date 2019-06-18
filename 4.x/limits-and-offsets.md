---
layout: project
version: 4.x
title: Limits and offsets
description: Learn about limits and offsets
---

Limiting the number of results returned by a query, is achieved by using the `limit` method.

{% capture tabs %}
{% capture php %}
```php
$result = $db->from('users')
             ->orderBy('name')
             ->limit(25)
             ->select()
             ->all();
```
{% endcapture %}
{% capture sql%}
```sql
SELECT * FROM `users` ORDER BY `name` ASC LIMIT 25
```
{% endcapture %}
{% capture tab_id %}{% increment tab_id %}{% endcapture %}
{% include tab.html id=tab_id title='PHP' content=php checked=true %}
{% include tab.html id=tab_id title='SQL' content=sql %}
{% endcapture %}
{% include tabs.html content=tabs %}

You can skip a certain number of records by using the `offset` method. 
The skipped records will not be added to the results set. 
This method can be used only in conjunction with the `limit` method.

{% capture tabs %}
{% capture php %}
```php
$result = $db->from('users')
             ->orderBy('name')
             ->limit(25)
             ->offset(10)
             ->select()
             ->all();
```
{% endcapture %}
{% capture sql%}
```sql
SELECT * FROM `users` ORDER BY `name` ASC LIMIT 25 OFFSET 10
```
{% endcapture %}
{% capture tab_id %}{% increment tab_id %}{% endcapture %}
{% include tab.html id=tab_id title='PHP' content=php checked=true %}
{% include tab.html id=tab_id title='SQL' content=sql %}
{% endcapture %}
{% include tabs.html content=tabs %}