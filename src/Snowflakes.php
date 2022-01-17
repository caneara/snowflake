<?php declare(strict_types=1);

namespace Snowflake;

trait Snowflakes
{
    /**
     * Bootstrap the trait.
     *
     */
    public static function bootSnowflakes() : void
    {
        static::creating(function($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = snowflake();
            }
        });
    }

    /**
     * Disable auto-incrementing integers.
     *
     */
    public function getIncrementing() : bool
    {
        return false;
    }
}
