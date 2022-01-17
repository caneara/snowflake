<?php declare(strict_types = 1);

/**
 * Generate a new Snowflake identifier.
 *
 */
function snowflake() : string
{
    return resolve('snowflake')->id();
}
