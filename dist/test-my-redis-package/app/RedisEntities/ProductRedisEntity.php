<?php

namespace App\RedisEntities;

use App\Models\Product;
use Hesam1996\RedisEntity\Support\BaseRedisEntity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProductRedisEntity extends BaseRedisEntity
{
    protected string $entity = 'products';

    protected ?string $model = Product::class;

    /**
     * Customize the query used to fetch records from the database.
     */
    protected function query(): Builder
    {
        return Product::query();
    }

    /**
     * Determine the Redis hash field name for a given model.
     */
    protected function hydrateKey(Model $model): string
    {
        return (string) $model->getKey();
    }

    /**
     * Determine the value stored in Redis for a given model.
     *
     * @return array<string, mixed>
     */
    protected function hydrateValue(Model $model): array
    {
        return $model->toArray();
    }
}
