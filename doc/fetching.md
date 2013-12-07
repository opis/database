##Fetching records##

Select all columns from a table and process the result set

```sql
SELECT * FROM `customers`
```

```php
$result = $db->from('customers')
             ->select();
             
foreach($result as $row)
{
    var_dump($row);
}
```

Select a single column from a table and process the result set

```sql
SELECT `id` FROM `customers`
```

```php
$result = $db->from('customers')
             ->select('id');

foreach($result as $row)
{
    print $row->id;
}
```

Select multiple columns from a table

```sql
SELECT `name`, `email` FROM `customers`
```

```php
$result = $db->from('customers')
             ->select(array('name', 'email'));

foreach($result as $row)
{
    print  $row->name . ' => ' . $row->email;
}
```

To fetch a single record from the result set use the **->first()** method

```sql
SELECT `name`, `email` FROM `customers`
```

```php
$result = $db->from('customers')
             ->first(array('name', 'email'));
             
print $result->name . ' => ' . $result->email;
```

To fetch a single column use the **->column()** method. 

```sql
SELECT `email` FROM `customers`
```

```php
$email = $db->from('customers')
             ->column('email');
             
print $email;
```

Aliasing column names

```sql
SELECT `phone_number` AS `phone`, `primary_email` AS `email` FROM `customers`
```

```php
$result = $db->from('customers')
             ->select(array('phone_number' => 'phone', 'primary_email' => 'email'));

//Processing the result set

foreach($result as $row)
{
    print $row->phone . ', ' . $row->email;
}
```

Selecting from multiple tables

```sql
SELECT * FROM `customers`, `products`
```

```php
$result = $db->from(array('customers', 'products'))
             ->select();
```

Aliasing table names

```sql
SELECT * FROM `customers` AS `c`, `products` AS `p`
```

```php
$result = $db->from(array('customers' => 'c', 'products' => 'p'))
             ->select();
```

Selecting distinct values from a table

```sql
SELECT DISTINCT * FROM `customers`
```

```php
$result = $db->from('customers')
             ->distinct()
             ->select();
```