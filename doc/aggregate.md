##Aggregate functions##

###Counting##

```sql
SELECT COUNT(*) FROM `customers`
```

```php
$result = $db->from('customers')
             ->count();
```

```sql
SELECT COUNT(`facebook_id`) FROM `customers`
```

```php
$result = $db->from('customers')
             ->count('facebook_id');
```

```sql
SELECT COUNT(DISTINCT `city`) FROM `customers`
```

```php
$result = $db->from('customers')
             ->count('city', true);
```

```sql
SELECT COUNT(DISTINCT `city`, `street`) FROM `customers`
```

```php
$result = $db->from('customers')
             ->count(array('city', 'street'));
```

###Average###

```sql
SELECT AVG(`price`) FROM `products`
```

```php
$result = $db->from('products')
             ->avg('price');
```

```sql
SELECT AVG(DISTINCT `price`) FROM `products`
```

```php
$result = $db->from('products')
             ->avg('price', true);
```

###Largest value###

```sql
SELECT MAX(`price`) FROM `products`
```

```php
$result = $db->from('products')
             ->max('price');
```

```sql
SELECT MAX(DISTINCT `price`) FROM `products`
```

```php
$result = $db->from('products')
             ->max('price', true);
```

###Smallest value###

```sql
SELECT MIN(`price`) FROM `products`
```

```php
$result = $db->from('products')
             ->min('price');
```

```sql
SELECT MIN(DISTINCT `price`) FROM `products`
```

```php
$result = $db->from('products')
             ->min('price', true);
```

###Total###

```sql
SELECT SUM(`quantity`) FROM `products`
```

```php
$result = $db->from('products')
             ->sum('quantity');
```

```sql
SELECT SUM(DISTINCT `quantity`) FROM `products`
```

```php
$result = $db->from('products')
             ->sum('quantity', true);
```