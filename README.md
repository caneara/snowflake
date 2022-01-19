<!-- Screenshot -->
<p align="center">
    <img src="resources/wallpaper.jpg" alt="Wallpaper">
</p>

<!-- Badges -->
<p align="center">
  <img src="resources/version.svg" alt="Version">
  <img src="resources/license.svg" alt="License">
</p>

# Snowflake

This package enables a Laravel application to create Twitter Snowflake identifiers. It is a very thin wrapper around the excellent [Snowflake PHP](https://github.com/godruoyi/php-snowflake) library created by Godruoyi.

## What are Snowflakes?

Snowflakes are a form of unique identifier devised by Twitter. In this respect, they are similar to other unique identifier algorithms such as UUID or ULID.

## Why should I use them?

I've written an [article](https://itnext.io/choosing-the-right-data-type-means-of-generating-unique-primary-keys-d7aac92968c6) exploring the benefits of Snowflakes over other unique identifiers. However, in short:

- They consists entirely of integers.
- They uses less space (16 characters, so it fits in a `BIGINT`).
- Indexing of integers is much faster than indexing a string.
- Keys begin with a timestamp, so are sortable.
- Keys end with a random number, so guessing table size is not possible.
- Databases handle integers more efficiently than strings.
- Generation of new keys is faster (less than 1 ms).

## Installation

Pull in the package using Composer:

```bash
composer require mattkingshott/snowflake
```

## Configuration

Snowflake includes a configuration file that allows you to set:

1. The data center number.
2. The worker node number.
3. The starting timestamp.
4. The sequence resolver.

Most developers won't need to alter these values unless they need to set up a distributed architecture for generating Snowflakes.

If you want to change any of the values, publish the configuration file using Artisan:

```bash
php artisan vendor:publish
```

## Usage

You can generate a Snowflake by resolving the service out of the container and calling its `id` method:

```php
resolve('snowflake')->id(); // (string) "5585066784854016"
```

> **WARNING**: Do not create instances of the Snowflake service, as doing so risks generating matching keys / introducing collisions. Instead, always resolve the Snowflake singleton out of the container. You can also use the global helper method (see below).

Since this is a little cumbersome, the package also registers a global `snowflake()` helper method that you can use anywhere.

```php
snowflake(); // (string) "5585066784854016"
```

## Databases

If you want to use Snowflakes in your database e.g. for primary and foreign keys, then you'll need to perform a couple of steps.

First, modify your migrations so that they use the Snowflake migration methods e.g.

```php
// Before
$table->id();
$table->foreignId('user_id');
$table->foreignIdFor(User::class);

// After
$table->snowflake()->primary();
$table->foreignSnowflake('user_id');
$table->foreignSnowflakeFor(User::class);
```

Here's an example:

```php
class CreatePostsTable extends Migration
{
    public function up()
    {
        Schema::create('posts', function(Blueprint $table) {
            $table->snowflake()->primary();
            $table->foreignSnowflake('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 100);
            $table->timestamps();
        });
    }
}
```

Next, if you're using Eloquent, add the package's `Snowflakes` trait to your Eloquent models:

```php
<?php

namespace App\Models;

use Snowflake\Snowflakes;

class Post extends Model
{
    use Snowflakes;
}
```

Finally, configure the model's `$casts` array to use the package's `SnowflakeCast` for all Snowflake attributes. This cast automatically handles conversion from `string` to `integer` and vice-versa when storing or fetching a Snowflake from the database. It also ensures that languages which do not support 64-bit integers (such as JavaScript), will not truncate the Snowflake.

```php
<?php

namespace App\Models;

use Snowflake\Snowflakes;
use Snowflake\SnowflakeCast;

class Post extends Model
{
    use Snowflakes;

    protected $casts = [
        'id'      => SnowflakeCast::class,
        'user_id' => SnowflakeCast::class,
        'title'   => 'string',
    ];
}
```

## Contributing

Thank you for considering a contribution to Snowflake. You are welcome to submit a PR containing improvements, however if they are substantial in nature, please also be sure to include a test or tests.

## Support the project

If you'd like to support the development of Snowflake, then please consider [sponsoring me](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=YBEHLHPF3GUVY&source=url). Thanks so much!

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
