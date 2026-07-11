<?php

declare(strict_types=1);

namespace Hesam1996\RedisEntity\Contracts;

use Illuminate\Support\Collection;

interface RedisEntityInterface
{
    /**
     * Retrieve all records of the entity.
     */
    public function getAll(): Collection;

    /**
     * Retrieve a single record by its identifier.
     */
    public function get(string|int $id): mixed;

    /**
     * Store or update a single record.
     */
    public function set(string|int $id, mixed $value): bool;

    /**
     * Store multiple records in a single batch operation.
     */
    public function setMany(iterable $items): bool;

    /**
     * Remove a single record.
     */
    public function forget(string|int $id): bool;

    /**
     * Determine whether a record exists.
     */
    public function has(string|int $id): bool;

    /**
     * Count the number of records.
     */
    public function count(): int;

    /**
     * Remove the entire entity.
     */
    public function clear(): bool;
}
