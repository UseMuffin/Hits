# Hits

[![Build Status](https://img.shields.io/travis/UseMuffin/Hits/master.svg?style=flat-square)](https://travis-ci.org/UseMuffin/Hits)
[![Coverage](https://img.shields.io/coveralls/UseMuffin/Hits/master.svg?style=flat-square)](https://coveralls.io/r/UseMuffin/Hits)
[![Total Downloads](https://img.shields.io/packagist/dt/muffin/hits.svg?style=flat-square)](https://packagist.org/packages/muffin/hits)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)

Hits (view) counter for CakePHP 3 ORM.

## Install

Using [Composer][composer]:

```shell
composer require muffin/hits:1.0.x-dev
```

You then need to load the plugin. You can use the shell command:

```shell
bin/cake plugin load Muffin/Hits
```

or by manually adding statement shown below to `bootstrap.php`:

```php
Plugin::load('Muffin/Hits');
```

## Usage

Setting up the behavior is similar to the `CounterCacheBehavior` in the sense that it takes
a list of fields as configuration. The field name could belong to the current table or to
another table (i.e. `view_count` or `OtherTable.posts_view_count`).

```php
$this->addBehavior('Muffin/Hits.Hits', ['view_count']);
```

Or more than just one field, the other one based on certain conditions:

```php
$this->addBehavior('Muffin/Hits.Hits', [
    // count only if the post is published
    'view_count' => ['is_published' => true],
    // count all views
    'total_view_count'
]);
```

Or based on certain options passed to the `Model.beforeFind` event (i.e. authenticated user):

```php
$this->addBehavior('Muffin/Hits.Hits', [
    // count only if the user viewing it is not an admin
    'view_count' => function ($field, \ArrayObject $options) {
        return !isset($options['_footprint'])
            || $options['_footprint']->is_admin === false;
    },
    // count all views
    'total_view_count'
]);
```

You could also define the value to increment the counter by (defaults to `1`):

```php
$this->addBehavior('Muffin/Hits.Hits', [
    'view_count' => ['increment' => 2]
]);
```

To use them all at once:

```php
$this->addBehavior('Muffin/Hits.Hits', [
    'view_count' => [
        'conditions' => ['is_published' => true],
        'callback' => function ($field, \ArrayObject $options) {
            return !isset($options['_footprint'])
                || $options['_footprint']->is_admin === false;
        },
        'increment' => 2,
    ],
    'total_view_count'
]);
```

That's it!

## Patches & Features

* Fork
* Mod, fix
* Test - this is important, so it's not unintentionally broken
* Commit - do not mess with license, todo, version, etc. (if you do change any, bump them into commits of
their own that I can ignore when I pull)
* Pull request - bonus point for topic branches

To ensure your PRs are considered for upstream, you MUST follow the [CakePHP coding standards][standards].

## Bugs & Feedback

http://github.com/usemuffin/hits/issues

## License

Copyright (c) 2015, [Use Muffin][muffin] and licensed under [The MIT License][mit].

[cakephp]:http://cakephp.org
[composer]:http://getcomposer.org
[mit]:http://www.opensource.org/licenses/mit-license.php
[muffin]:http://usemuffin.com
[standards]:http://book.cakephp.org/3.0/en/contributing/cakephp-coding-conventions.html
