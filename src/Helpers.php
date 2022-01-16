<?php declare(strict_types = 1);

/**
 * Generate a new Snowflake identifier.
 *
 */
function snowflake() : int
{
    return (int) resolve('snowflake')->id();
}
