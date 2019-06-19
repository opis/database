---
layout: project
version: 3.x
title: Results handling
description: Learn how to handle the result set of a query
canonical: /database/4.x/results-handling.html
---

As you have already learned, [fetching records](fetching-records) 
is done by using the `select` method. In order to improve performance and to
 provide developers a greater control over the result set returned by a query, 
instead of simply returning a list with results, the `select` method returns a 
cursor to a result set, represented by an instance of the `Opis\Database\ResultSet` class. 
This class contains various methods used in data handling, including the `all` method 
that was presented in the previous section. 

## Using the cursor

Using the `all` method is perfectly fine when we are dealing with reasonable 
data sets, but we can quickly run out of memory if we are dealing with large data sets.
 This is because the `all` method transfers all the results from the database into PHP's 
memory and stores them into an array. One solution to avoid going out of memory when 
dealing with large data sets is to iterate through the entire result set and process one result at a time. 

```php
$result = $db->from('users')
             ->select(['name', 'email']);
             
while(false !== $user = $result->next())
{
    echo $user->name, $user->email;
}
```

## Fetching options

By default the results of a query are fetched as anonymous objects with property 
names that correspond to the column names returned in your result set.
You can change this behavior by using a set of methods provided by the 
`Opis\Database\ResultSet` class.

Fetching records as an array indexed by column name is done using the `fetchAssoc` method.  

```php
$result = $db->from('users')
             ->select(['name', 'email'])
             ->fetchAssoc()
             ->all();
             
foreach($result as $user)
{
    echo $user['name'], $user['email'];
}
```

Fetching records as an array indexed by column number, starting at column 0, 
is done using the `fetchNum` method. 

```php
$result = $db->from('users')
             ->select(['name', 'email'])
             ->fetchNum()
             ->all();
             
foreach($result as $user)
{
    echo $user[0], $user[1];
}
```

The `fetchBoth` method fetch each record as an array indexed by both column name and column number. 

```php
$result = $db->from('users')
             ->select(['name', 'email'])
             ->fetchBoth()
             ->all();
             
foreach($result as $user)
{
    //prints name and email
    echo $user['name'], $user[1];
}
```

The `fetchNamed` method is similar to `fetchAssoc` method, except that if there
are multiple columns with the same name, the value referred to by that key will
be an array of all the values in the row that had that column name. 

You can map the columns of the result set to named properties in a custom class 
by using the `fetchClass` method. The method accepts as arguments a class name 
and optionally an array of arguments that will be passed to the class constructor. 

The named properties of your class that will be mapped to column
names must have `public` access. 
{:.alert.alert-warning data-title="Important"}

```php
class User
{
    public $name;
    public $email;
    
    public function test()
    {
        echo $this->name, $this->email;
    }
}

$result = $db->from('users')
             ->select(['name', 'email'])
             ->fetchClass('User')
             ->all();
             
foreach($result as $user)
{
    $user->test();
}
```

## Callback functions

**Opis Database** allows you to specify a callback function that can be passed 
as an argument to the `all` and the `first` methods. Records are mapped to the 
result of calling the callback function using each row's columns as parameters in the call. 

```php
class User
{
    protected $name;
    protected $email;
    
    public function __construct($name, $email)
    {
        $this->name = $name;
        $this->email = $email;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function sendEmail($subject, $message)
    {
        mail($this->email, $subject, $message);
    }
    
}

$result = $db->from('users')
             ->select(['name', 'email'])
             ->all(function($name, $email){
                return new User($name, $email);
             });

$message = "Opis Database is great! http://www.opis.io/database";
             
foreach($result as $user)
{
    $subject = "Hello " . $user->getName();
    $user->sendEmail($subject, $message);
}
```