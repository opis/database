---
layout: project
version: 3.x
title: Relationships
description: Learn about ORM relationships
---

The relationships between tables can be described by adding methods to your model 
class and utilize the `hasOne`, `hasMany`, `belongsTo` and `belongsToMany`, inside those methods,
to define the type of the relationship between two models.

The related models can be then retrieved from a model instance by getting the value 
of the property that has the same name as the relationship method.

```php
class User extends BaseModel
{
    // Define a relationship 
    public function articles()
    {
        return $this->hasMany('Article');
    }
}

// ...

$user = User::find(1);

// Get the related models
foreach ($user->articles as $article) {
    echo $article->title, PHP_EOL;
}
```

## One to one

A one-to-one relationship is a relation where each record of a table has a single 
related record into another table. For example a `User` might be associated with a `Profile`. 
Defining the relationship between `User` and `Profile` can be done by placing a 
`profile` method on the `User` model. The `profile` method must return the result of 
calling the `hasOne` method.

```php
class Profile extends BaseModel
{
    // ...
}

class User extends BaseModel
{
    public function profile()
    {
        return $this->hasOne('Profile');
    }
}

// ...

$user = User::find(1);

echo $user->profile->description;
```

The first argument passed to the `hasOne` method must be the class name of the related model. 
If the model class is under a namespace, then the namespace must be included as well.

```php
return $this->hasOne('\My\Namespace\Profile');
```

**Opis Database ORM** assumes that the foreign key of the relationship is based on 
the related model's name. In this particular case, the `Profile` model is assumed 
to have a `user_id` foreign key. If the related model has a differently named foreign key, 
you can specify this by passing the foreign key's name as a second argument to the `hasOne` method.

```php
return $this->hasOne('Profile', 'foreign_key');
```

#### The inverse of the relation 
{: #has-one-inverse }

Defining the inverse of a one-to-one relationship is done by using the `belongsTo` method.

```php
class Profile extends BaseModel
{
    public function user()
    {
        return $this->belongsTo('User');
    }
}

class User extends BaseModel
{
    public function profile()
    {
        return $this->hasOne('Profile');
    }
}

// ...

$profile = Profile::find(1);

echo $profile->user->name;
```

The first argument passed to the `belongsTo` method must be the class name of the parent model.
If the model class is under a namespace, then the namespace must be included as well.

```php
return $this->belongsTo('\My\Namespace\User');
```

If the model foreign key's name is different from the name assumed by **Opis Database**
(parent table name + `_id` suffix), than you can specify the foreign key's name by passing it
as the second argument to the `belongsTo` method.

```php
return $this->belongsTo('User', 'foreign_key');
```

## One to many

A one-to-many relationship is a relation where each record of a table might have 
multiple related records into another table. For example, a `User` might be associated 
with multiple `Articles`. Defining the relationship between `User` and `Article` can be done 
by placing an `articles` method on the `User` model. The `articles` method must return 
the result of calling the `hasMany` method.

```php
class Article extends BaseModel
{
    // ...
}

class User extends BaseModel
{
    public function articles()
    {
        return $this->hasMany('Article');
    }
}

// ...

$user = User::find(1);

foreach($user->articles as $article)
{
    echo $article->title, PHP_EOL;
}
```

The first argument passed to the `hasMany` method must be the class name of the related model. 
If the model class is under a namespace, then the namespace must be included as well.

```php
return $this->hasMany('\My\Namespace\Article');
```

If the related model's foreign key doesn't follow the **Opis Database** naming convention
(parent table name + `_id` suffix), you may specify the foreign key's name by passing it 
as the second argument to the `hasMany` method.

```php
return $this->hasMany('Article', 'foreign_key');
```

#### The inverse of the relation 
{: #has-many-inverse }

As in the case of the one-to-one relationships, the inverse of a one-to-many relationship 
can be defined by using the `belongsTo` method.

```php
class Article extends BaseModel
{
    public function author()
    {
        return $this->belongsTo('User');
    }
}

class User extends BaseModel
{
    public function articles()
    {
        return $this->hasMany('Article');
    }
}

// ...

$article = Article::find(1);

echo $article->author->name;
```

## Many to many

A many-to-many relationship is a relation where a record from a table might share 
with other records from the same table, multiple related records that are stored into another table.

An example of such a relationship is a user with many roles whom also might be shared by other users. 
In order to define such a relationship, there must be a [junction table] between the two related tables.

Defining a many-to-many relationship is done by using the `belongsToMany` method. 
The method takes as an argument the full class name(including the namespace) of the related model.

```php
class User extends BaseModel
{
    public function roles()
    {
        return $this->belongsToMany('Role');
    }
}

class Role extends BaseModel
{
    public function users()
    {
        return $this->belongsToMany('User');
    }
}

// ...

$user = User::find(1);

foreach($user->roles as $role)
{
    echo $role->name, PHP_EOL:
}

$role = Role::find(1);

foreach($role->users as $user)
{
    echo $user->name, PHP_EOL;
}
```

#### Naming conventions

The junction table name is assumed to be formed from the names of the related tables 
taken in alphabetical order and separated by an underscore. So, if we have two related tables 
named `foo` and `bar`, the junction table will be `bar_foo`. If the name of your junction table 
doesn't follow this naming convention, then you must pass the name of your junction table as the 
third argument to the `belongsToMany` method.

```php
return $this->belongsToMany('Role', null, 'junction_table');
```

The junction table must define two columns containing foreign keys to the related tables. 
If we have to related tables `foo` and `bar`, the column names must be `foo_id` and `bar_id`.

If the foreign key that points from the junction table back to the model doesn't follow 
the naming convention, you must pass the name of the foreign key as the second argument 
to the `belongsToMany` method.

```php
return $this->belongsToMany('Role', 'fk_user');
```

If the foreign key that points from the junction table to the related model doesn't 
follow the naming convention, you must pass the name of the foreign key as the fourth 
argument to the `belongsToMany` method.

```php
return $this->belongsToMany('Role', null, null, 'fk_role');
```

[junction table]: http://en.wikipedia.org/wiki/Junction_table "Junction table"
{:rel="nofollow" target="_blank"} 
