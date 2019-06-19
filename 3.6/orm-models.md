---
layout: project
version: 3.x
title: Working with models
description: Learn how to work with models
---

## Create related records

Let's assume we have a `User` and an `Article` model defined like bellow.

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
```

When you want to create a new `Article` you can specify its author by setting an 
instance of the `User` model as a value to the `author` property.

```php
$user = User::find(1);

$article = new Article;

$article->title = "Some title";
$article->content = "Some content";

$article->author = $user;

$article->save();
```

Of course, in this way, you can also change the author of an article, if you want.

```php
$user = User::find(2);
$article = Article::find(1);

$article->author = $user;

$article->save();
```

#### Handling many-to-many relationships

The `link` and the `unlink` methods are available 
starting with version `3.3.0`.
{:.alert.alert-warning data-title="Important"}

Creating related models that have a many-to-many relationship is a bit different since 
they use a junction table. Linking together two related models is done by using the `link` method.

```php
$user = User::find(1);
$role = Role::find(1);

$user->roles()->link($role);

// The above produce the same result as the following

$role->users()->link($user);
```

The `link` method takes as an argument a related model instance, 
the related model's ID or an array of related models or IDs.

```php
$user = User::find(1);

// Link multiple records using an array of models
$user->roles()->link(Role::findAll());

// Link a single record using the record's ID
$user->roles()->link(1);

// Link multiple records using an array of IDs
$user->roles()->link([1,2,3]);
```

Unlinking related models is done with the help of the `unlink` method. 
The method takes the same arguments as the `link` method.

```php
$user = User::find(1);
$role = Role::find(1);

// Unlink a single record using a model instance
$user->roles()->unlink($role);

// Unlink multiple records using an array of models
$user->roles()->unlink(Role::findAll());

// Unlink a single record using the record's ID
$user->roles()->unlink(1);

// Unlink multiple records using an array of IDs
$user->roles()->unlink([1,2,3]);
```

## Eager loading

Loading related records may cause sometimes the `N + 1` query problem. To illustrated this problem, 
let's consider an `Article` model and a `User` model.

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
```

Now, let's retrieve the first 10 articles and display the name of their author.

```php
$articles = Article::limit(10)->orderBy('title')->all();

foreach($articles as $article)
{
    echo $article->author->name;
}
```

The above code will execute 1 query to retrieve 10 articles and then, for each of 
the 10 articles it will execute a query to retrieve the article's author, 
meaning that 11 queries will be executed in total.

We can reduce this operation to just 2 queries by using eager loading. 
Specifying which relationships to be eager loaded is done with the help of the `with` method.

```php
$articles = Article::with('author')->limit(10)->orderBy('title')->all();

foreach($articles as $article)
{
    echo $article->author->name;
}
```

#### Multiple relationships

If you need to eager load multiple relationships, just pass to the with method an
array containing the names of the relationships.

```php
$articles = Article::with(['author', 'comments'])
                   ->limit(10)->orderBy('title')->all();
```

#### Nested relationships

Eager loading nested relationships is done by using the dot notation. 
For example we can eager load the article's author and the author's profile in a single statement.

```php
$articles = Article::with('author.profile')
                   ->limit(10)->orderBy('title')->all();
```

#### Deferred execution

By default, the execution of the eager loading query will be deferred until the 
dynamic property associated with the relationship is accessed for the first time.

```php
// Articles are loaded. The execution of the eager loading query is deferred.
$articles = Article::with('author')->limit(10)->orderBy('title')->all();

foreach($articles as $article)
{
    // The eager loading query is executed (once only).
    echo $article->author->name;
}
```

Changing this behavior is done by passing `true` as the second argument to the `with` method.

```php
// Articles are loaded and the eager loading query is executed.
$articles = Article::with('author', true)->limit(10)->orderBy('title')->all();

foreach($articles as $article)
{
    echo $article->author->name;
}
```