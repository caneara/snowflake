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

## Promo

[Lumeno](https://lumeno.dev) centralizes your IT profile (résumé, project portfolio and written articles) so that employers can discover, and invite you to apply for jobs that match your personal requirements, such as tech skills, minimum salary, availability, location, commute distance, and much more... [sign up for free!](https://lumeno.dev)

<!-- Screenshot -->
<p align="center">
    <a target="_blank" href="https://lumeno.dev">
        <img src="resources/banner.png" alt="Lumeno" style="max-height: 170px">
    </a>
</p>

## Configuration

Snowflake includes a configuration file with several settings that you can use to initialize the Snowflake service. You should begin by publishing this configuration file:

```bash
php artisan vendor:publish
```

### Distributed architecture

The service allows for the use of a distributed architectural setup involving data centers and worker nodes that are each responsible for generating Snowflakes according to their own designated identifiers. For maximum flexibility, as well as backward compatibility, this is the default configuration.

If you do not intend to run a distributed architectural setup, then your first step should be to set the corresponding configuration value to `false`.

### Data centers and worker nodes

When using a distributed architectural setup, you'll need to set the data center and worker node that the application should use when generating Snowflakes. These are both set to `1` by default, as that is a good starting point, but you are free to increase these numbers as you add more centers and nodes.

The maximums for each of these configuration values is `31`. This gives you up to 31 nodes per data center, and 31 data centers in total. Therefore, you can have up `961` worker nodes each generating unique Snowflakes.

> If you have disabled distributed architecture, then you can skip the data center and worker node values as they will be ignored by the service.

### Starting timestamp

The service compares the Unix Epoch with the given starting timestamp as part of the process in generating a unique Snowflake. As a result, Snowflakes can be generated for up to 69 years using any given starting timestamp.

In most cases, you should set this value to the current date using a format of `YYYY-MM-DD`.

> Do not set the timestamp to a date in the future, as that won't achieve anything. You should also avoid using a date in the past, as that may reduce the number of years for which you can generate timestamps.

### Sequence resolver

In order to handle the generation of unique keys within the same millisecond, the service uses a sequence resolver. There are several to choose from, however they each have dependencies, such as Redis. You are free to use any of them, however the default option is a good choice, as it **doesn't** have any dependencies.

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
