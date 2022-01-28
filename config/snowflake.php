<?php declare(strict_types = 1);

use Godruoyi\Snowflake\RandomSequenceResolver;

return [

    /*
    |--------------------------------------------------------------------------
    | Distributed Architecture
    |--------------------------------------------------------------------------
    |
    | This value determines whether Snowflakes should be generated with random
    | data center and worker node values (see below). When set to false, the
    | center and node values in this configuration file are ignored.
    |
    */

    'distributed' => true,

    /*
    |--------------------------------------------------------------------------
    | Data Center
    |--------------------------------------------------------------------------
    |
    | This value represents the data center reference that should be used by
    | Snowflake when generating unique identifiers. The value must be 1 - 31.
    |
    */

    'data_center' => 1,

    /*
    |--------------------------------------------------------------------------
    | Worker Node
    |--------------------------------------------------------------------------
    |
    | This value represents the worker node reference that should be used by
    | Snowflake when generating unique identifiers. The value must be 1 - 31.
    |
    */

    'worker_node' => 1,

    /*
    |--------------------------------------------------------------------------
    | Start Timestamp
    |--------------------------------------------------------------------------
    |
    | This value represents the starting date for generating new timestamps.
    | Snowflakes can be created for 69 years past this date. In most cases,
    | you should set this value to the current date when building a new app.
    |
    */

    'start_timestamp' => '2022-01-01',

    /*
    |--------------------------------------------------------------------------
    | Sequence Resolver
    |--------------------------------------------------------------------------
    |
    | This value represents the sequencing strategy that should be used to
    | ensure that multiple Snowflakes generated within the same millisecond
    | are unique. The default is a good choice, as it has no dependencies.
    |
    */

    'sequence_resolver' => RandomSequenceResolver::class,

];
