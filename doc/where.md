##WHERE Clauses##

####Using **->where()**, **->andWhere()**, **orWhere()** methods.####

```sql
SELECT * FROM `products` WHERE `quantity` = 10
```

```php
$result = $db->from('products')
             ->where('quantity', 10)
             ->select();
```

```sql
SELECT * FROM `products` WHERE `quantity` > 10
```

```php
$result = $db->from('products')
             ->where('quantity', 10, '>')
             ->select();
```

```sql
SELECT * FROM `products` WHERE `quantity` > 10 AND `quantity` < 20
```

```php
$result = $db->from('products')
             ->where('quantity', 10, '>')
             ->where('quantity', 20, '<')
             ->select();

//Alternative syntax

$result = $db->from('products')
             ->where('quantity', 10, '>')
             ->andWhere('quantity', 20, '<')
             ->select();
```

There is no difference between **->where()** and **->andWhere()** methods. The main purpose of the **->andWhere()** method is to improve code readability.

```sql
SELECT * FROM `products` WHERE `brand` = 'Toshiba' OR `brand` = 'Sony'
```

```php
$result = $db->from('products')
             ->where('brand', 'Toshiba')
             ->orWhere('brand', 'Sony')
             ->select();
```

Grouping conditions

```sql
SELECT * FROM `products` WHERE `category` = 'laptops' AND (`brand` = 'Toshiba' OR `brand` = 'Sony')
```

```php
$result = $db->from('products')
             ->where('category', 'laptops')
             ->andWhere(function($group){
                $group->where('brand', 'Toshiba')
                      ->orWhere('brand', 'Sony');
             })
             ->select();
```

####Using **->whereNull()**, **->andWhereNull()**, **->orNull()**.####