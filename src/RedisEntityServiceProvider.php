<?php

declare(strict_types=1);

namespace Hesam1996\RedisEntity;

use Illuminate\Support\ServiceProvider;
use Hesam1996\RedisEntity\Console\MakeRedisEntityCommand;

class RedisEntityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/redis-entity.php',
            'redis-entity'
        );
    }

    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            MakeRedisEntityCommand::class,
        ]);

        $this->publishes([
            __DIR__ . '/../config/redis-entity.php' => config_path('redis-entity.php'),
        ], 'redis-entity-config');

        $this->publishes([
            __DIR__ . '/stubs' => base_path('stubs'),
        ], 'redis-entity-stubs');
    }
}
