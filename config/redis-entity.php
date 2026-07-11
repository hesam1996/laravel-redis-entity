<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Namespace
    |--------------------------------------------------------------------------
    |
    | The namespace for generated Redis entity classes. These classes will be
    | placed under this namespace by default.
    |
    */
    'namespace' => 'App\\RedisEntities',

    /*
    |--------------------------------------------------------------------------
    | Default Path
    |--------------------------------------------------------------------------
    |
    | The directory path (relative to app/) where generated Redis entity
    | classes will be stored.
    |
    */
    'path' => 'app/RedisEntities',

    /*
    |--------------------------------------------------------------------------
    | Redis Connection
    |--------------------------------------------------------------------------
    |
    | The Redis connection name that will be used by all Redis entities.
    | This should match one of the connections defined in config/database.php
    | under the 'redis' array.
    |
    */
    'connection' => env('REDIS_ENTITY_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Key Prefix
    |--------------------------------------------------------------------------
    |
    | A global prefix for all Redis keys managed by entities. This helps
    | namespace your keys and avoid collisions with other Redis data.
    |
    */
    'prefix' => env('REDIS_ENTITY_PREFIX', 'entity'),
];
