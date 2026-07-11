<?php

declare(strict_types=1);

namespace Hesam1996\RedisEntity\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use JsonException;
use Hesam1996\RedisEntity\Contracts\RedisEntityInterface;

abstract class BaseRedisEntity implements RedisEntityInterface
{
    /**
     * The entity name (defined by the child class).
     */
    protected string $entity;

    /**
     * The Eloquent model used as the fallback data source.
     * When null, the entity works in pure Redis mode (no hydration).
     *
     * @var class-string<Model>|null
     */
    protected ?string $model = null;

    /**
     * The Redis connection; resolved from config when null.
     */
    protected ?string $connection = null;

    /**
     * Time-to-live in seconds; null means no expiration.
     */
    protected ?int $ttl = null;

    public function getAll(): Collection
    {
        $this->warmIfCold();

        /** @var array<string, string> $items */
        $items = $this->redis()->hgetall($this->key());

        return collect($items)->map(
            fn (string $value): mixed => $this->unserialize($value)
        );
    }

    public function get(string|int $id): mixed
    {
        $this->warmIfCold();

        $value = $this->redis()->hget($this->key(), (string) $id);

        if ($value !== null) {
            return $this->unserialize($value);
        }

        // The hash is warm but this specific record is missing:
        // fall back to a single database lookup.
        return $this->resolveFromModel($id);
    }

    public function set(string|int $id, mixed $value): bool
    {
        $result = $this->redis()->hset(
            $this->key(),
            (string) $id,
            $this->serialize($value)
        );

        $this->applyTtl();

        return $result !== false;
    }

    public function setMany(iterable $items): bool
    {
        $payload = [];

        foreach ($items as $id => $value) {
            $payload[(string) $id] = $this->serialize($value);
        }

        if ($payload === []) {
            return true;
        }

        $this->redis()->hmset($this->key(), $payload);
        $this->applyTtl();

        return true;
    }

    public function forget(string|int $id): bool
    {
        return (int) $this->redis()->hdel($this->key(), [(string) $id]) > 0;
    }

    public function has(string|int $id): bool
    {
        return (bool) $this->redis()->hexists($this->key(), (string) $id);
    }

    public function count(): int
    {
        return (int) $this->redis()->hlen($this->key());
    }

    public function clear(): bool
    {
        return (int) $this->redis()->del($this->key()) > 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Model Hydration (Read-Through)
    |--------------------------------------------------------------------------
    */

    /**
     * Warm the cache from the database when the hash does not exist yet.
     * Does nothing in pure Redis mode (no model defined).
     */
    protected function warmIfCold(): void
    {
        if ($this->model === null || (bool) $this->redis()->exists($this->key())) {
            return;
        }

        Cache::lock("redis-entity:warm:{$this->entity}", 10)->block(5, function (): void {
            // Re-check inside the lock; another process may have warmed it already.
            if (! (bool) $this->redis()->exists($this->key())) {
                $this->warm();
            }
        });
    }

    /**
     * Load all records from the model and push them into Redis.
     */
    protected function warm(): void
    {
        $payload = $this->query()
            ->get()
            ->mapWithKeys(fn (Model $record): array => [
                (string) $this->hydrateKey($record) => $this->hydrateValue($record),
            ]);

        $this->setMany($payload);
    }

    /**
     * Fetch a single missing record from the database and cache it.
     */
    protected function resolveFromModel(string|int $id): mixed
    {
        if ($this->model === null) {
            return null;
        }

        $record = $this->query()->whereKey($id)->first();

        if ($record === null) {
            return null;
        }

        $value = $this->hydrateValue($record);

        $this->set($this->hydrateKey($record), $value);

        return $value;
    }

    /**
     * The base query used for hydration. Override to add
     * constraints, eager loading, or column selection.
     */
    protected function query(): Builder
    {
        return $this->model::query();
    }

    /**
     * The hash field name for a given record. Defaults to the primary key.
     */
    protected function hydrateKey(Model $record): string|int
    {
        return $record->getKey();
    }

    /**
     * The value stored in Redis for a given record.
     * Override to store a subset of attributes or a DTO-like array.
     */
    protected function hydrateValue(Model $record): mixed
    {
        return $record->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Internals
    |--------------------------------------------------------------------------
    */

    protected function key(): string
    {
        return sprintf('%s:%s', config('redis-entity.prefix'), $this->entity);
    }

    protected function redis(): Connection
    {
        return Redis::connection(
            $this->connection ?? config('redis-entity.connection')
        );
    }

    /**
     * @throws JsonException
     */
    protected function serialize(mixed $value): string
    {
        return json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @throws JsonException
     */
    protected function unserialize(string $value): mixed
    {
        return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }

    protected function applyTtl(): void
    {
        if ($this->ttl !== null) {
            $this->redis()->expire($this->key(), $this->ttl);
        }
    }
}
