---
layout: project
version: 3.x
title: Basic operations
description: Learn how to use Opis Database ORM
---
# Basic operations

1. [Finding records by primary key](#finding-records-by-primary-key)
2. [Accessing column values](#accessing-column-values)
3. [Performing queries](#performing-queries)
4. [Inserting records](#inserting-records)
5. [Updating records](#updating-records)
6. [Mass assignment](#mass-assignment)
7. [Deleting records](#deleting-records)

## Finding records by primary key

Finding a record by its primary key is done by using the `find` method. 
This method returns `false` if no records were found.

```php
$user = User::find(1);

if($user !== false)
{
    // do something
}
```

If you want to find multiple records by their primary key, you can use the `findMany`
method which will return an array of models, or an empty array if no records were found. 
This method takes as an argument an array containing primary key values.

```php
$users = User::findMany([1, 2, 3]);

foreach($users as $user)
{
    // do something
}
```

You can load all records from a model's table by using the `findAll` method.

```php
$users = User::findAll();

foreach($users as $user)
{
    // do something
}
```

You can specify which columns to be included by passing an array of column names 
to the `find`, `findMany` or `findAll` methods.

```php
$user = User::find(1, ['name', 'age']);

$users = User::findMany([1, 2, 3], ['name', 'age']);

$users = User::findAll(['name', 'age']);
```

## Accessing column values

Accessing the column values of the model is done by accessing the corresponding property 
of the model instance.

```php
foreach(User::findAll() as $user)
{
    echo $user->name, PHP_EOL;
}
```

You can alias a column's name by setting a `$mapColumns` property on the model's class.

```php
class User extends BaseModel
{
    protected $mapColumns = ['registration_email' => 'registrationEmail'];
}
```

In the above example, a column named `registration_email` was mapped to `registrationEmail`. 
Now we can access the `registrationEmail` property on a model instance to retrieve 
the value of the `registration_email` column.

```php
foreach(User::findAll() as $user)
{
    echo $user->registrationEmail, PHP_EOL;
}
```

#### Mutators

Mutators allow to intercept and change the value that is going to be set to a property of the model. 
Defining a mutator is done by creating a method that has the same name as the property, 
followed by the `Mutator` suffix.

```php
class User extends BaseModel
{
    public function passwordMutator($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }
}

// ...

$user = new User;

$user->name = 'John Doe';
$user->password = $secret;

$user->save();
```

#### Accessors

Accessors allow you to intercept and modify the value returned by accessing a 
property of a model. You can define an accessor for a model's property by adding 
a method with the same name as the property, followed by the `Accessor` suffix.

```php
class User extends BaseModel
{
    public function registrationDateAccessor($value)
    {
        return new DateTime($value);
    }
}

// ...

$user = User::find(1);

echo $user->registrationDate->format('Y-m-d H:i:s');
```

#### Type casting

You can cast a column's value from one type to another by using the `$cast` property. 
The supported cast types are: `integer`, `float`, `string`, `boolean`, `date` and `array`.

```php
class User extends BaseModel
{
    protected $cast = ['is_admin' => 'boolean'];
    
    protected $mapColumns = ['is_admin' => 'isAdmin'];
    
}

// ...

$user = User::find(1);

if($user->isAdmin)
{
    // do something
}
```

The array type cast is used to deserialize *JSON* into a *PHP* array. 
You can modify that array and set it back as property and the array will be automatically 
serialized back into *JSON*.

```php
class Page extends BaseModel
{
    protected $cast = ['keywords' => 'array'];
    
}

// ...

$page = Page::find(1);

$keywords = $page->keywords;

if(!in_array('ORM', $keywords))
{
    $keyowrds[] = 'ORM';
}

$page->keywords = $keywords;
$page->save();
```

#### Custom type casting

**Important!**{:.important}
This feature is available starting with version `3.3.2`.
{:.alert.alert-warning}


You can define a new casting type or how a casting operation is handled by using
the `$castType` property. If your cast callback is a non-static method on the current class, 
just use as a callback the method's name prefixed with `@`. The callback takes as arguments 
the cast's name and the value that needs to be casted.

```php
class User extends BaseModel
{
    protected $cast = [
        'registration_date' => 'date',
        'is_admin' => 'boolean',
    ];
    
    protected $castType = [
        'boolean' => [__CLASS__, 'castBoolean'],
        'date' => '@castDate'
    ];
    
    
    // Non-static handler
    protected function castDate($cast, $value)
    {
        // ...
    }
    
    // Static handler
    protected static function castBoolean($cast, $value)
    {
        // ...
    }
}
```

## Performing queries

Since model classes are basically build over a query builder, you can use them to 
perform complex queries by adding [filters], [limits], [joins] or [ordering criteria] to your queries. 
Retrieving the results of a query is done by using the `all` method. The method returns 
an array of models or an empty array if no records were found.

```php
$users = User::where('age')->atLeast(18)
             ->orderBy('age')
             ->all();
```

Retrieving only the first result of a query is done by using the `first` method. 
This method returns `false` if no records were found.

```php
$user = User::where('age')->atLeast(18)
            ->orderBy('age')
            ->limit(1)
            ->first();
```

#### Query scopes

Scopes allow you to define sets of constraints that you can utilize by calling a single method.
Defining a scope is simply a matter of adding a method that contains the `Scope` suffix. 
Scopes must always return an instance of the query builder.

```php
class User extends BaseModel
{
    public function fullAgedScope($query)
    {
        return $query->where('age')->atLeast(18);
    }
}
```

To use the newly created scope simply make a call that utilizes the scope method's 
name without the `Scope` suffix.

```php
// Return a list of full-aged users
$users = User::fullAged()
             ->orderBy('name')
             ->all();
```

#### Joins

**Important!**{:.important}
All records returned by a query that uses `joins` 
will be marked as [read-only records](#read-only-records).
{:.alert.alert-warning}

A possible use case for joins is when you want, for example, to return a list with 
all users that have written at least one article.

```php
$users = User::join('articles', function($join){
                $join->on('users.id', 'articles.user_id');
             })
             ->all();
```

The above example will return duplicates if a user have written multiple articles. 
This can be solved by selecting only the distinct users with the help of the `distinct` method.

```php
$users = User::join('articles', function($join){
                $join->on('users.id', 'articles.user_id');
             })
             ->distinct()
             ->all();
```

#### Single column's value 
{: #single-column-s-value}

Retrieving a single column's value is done by using the `column` method. 
The method returns the scalar value of the column or `false` if no records were found.

```php
$name = User::where('id')->is(1)->column('name');
```

#### Aggregates

You can also use all the [aggregates](aggregate-functions) functions. 
These methods return scalar values.

```php
$count = User::count();

$max = User::max('age');

$avg = User::avg('age');
```

## Inserting records

Inserting new records is done by creating a new instance of a model, setting the desired attributes
on the model's instance and then calling the model's `save` method.

```php
$user = new User;

$user->name = 'John Doe';
$user->email = 'foo@example.com';
$user->age = 18;

$user->save();
```

## Updating records

Updating a model consists in retrieving the model, setting the properties you want to update,
and finally calling the `save` method.

```php
$user = User::find(1);

$user->email = 'bar@example.com';

$user->save();
```

You can also update models that match a given query by using the `update` method.

```php
User::where('id')->is(1)
    ->update(['is_active' => true]);
```

#### Read-only records

By default, the models retrieved using join clauses are marked as *read-only*. 
Trying to save or to update a *read-only* model will result into an exception being thrown. 
A model can also be explicitly marked as being *read-only* by setting the model's 
`$readonly` property to true.

```php
class Comment extends BaseModel
{
    protectd $readonly = true;
}
```

## Mass assignment

Another way of creating or updating models is through mass assignment. 
Creating a model through mass assignment is done by using the `create` method. 
The method returns the instance of the inserted model.

```php
$user = User::create(['name' => 'John Doe',
                      'email' => 'foo@example.com',
                      'age' => 18]);
```

Updating an existing model is done by using the `assign` method on the model's instance.

```php
$user = User::find(1);

$user->assign(['email' => 'bar@example.com']);

$user->save();
```

#### Fillable attributes

You can define which properties of the model can be set through mass assignment 
by using the `$fillable` property. The property must contain an array with all 
the column names that can receive values through mass assignment.

```php
class User extends BaseModel
{
    protected $fillable = ['name', 'email', 'age'];
}
```

#### Guarded attributes

Defining which properties of a model are not fillable is done by using the `$guarded` property.
The property must contain an array with all the column names that can not receive 
values through mass assignment.

**Important!**{:.important}
The `$guarded` attributes serve as a *black list* while 
the `$fillable` attributes serve as a *white list*. You should only use one of this two, not both.
{:.alert.alert-warning}

```php
class User extends BaseModel
{
    protected $guarded = ['id', 'is_active', 'is_admin'];
}
```

## Deleting records

Deleting a record form the database can be done by calling the `delete` method on a model's instance.

```php
$user = User::find(1);

$user->delete();
```

You can also use a query if you want to select a set of models that needs to be deleted.

```php
User::where('age')->lessThan(18)
    ->delete();
```

[filters]: filters "Filters"
[limits]: limits-and-offsets "Limits and offsets"
[joins]: joins "Joins"
[ordering criteria]: ordering-criteria "Ordering criteria"