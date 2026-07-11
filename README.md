# Laravel Redis Entity

A lightweight Laravel package for managing Redis-backed entities with automatic
Read-Through caching and Cache Stampede protection.

It stores each entity as a **Redis Hash** and can transparently fall back to an
Eloquent model when the cache is cold — no manual cache handling needed.

---

## Installation
```bash
composer require hesam1996/laravel-redis-entity

php artisan vendor:publish --tag=redis-entity-config
```

## Quick Start

Generate a pure Redis entity:

```bash
php artisan redis-entity:make sessions
```

Generate an entity backed by an Eloquent model (Read-Through Cache):

```bash
php artisan redis-entity:make products --model=Product
```

Usage:

```php
$products = new ProductsEntity();

$products->getAll();      // first call → DB, next calls → Redis
$products->get(42);       // single record
$products->set('42', [...]);
$products->forget('42');
$products->clear();       // invalidate cache, next read re-warms from DB
```

## How it works

1️⃣ You call `getAll()` or `get($id)` on an entity

2️⃣ `warmIfCold()` checks if the Redis Hash exists

3️⃣ If cache is cold and a model is defined:
- a distributed lock is acquired (`Cache::store('redis')->lock()`)
- only **one** process fetches records from the database
- records are stored in Redis via `hydrateKey()` / `hydrateValue()`

4️⃣ Concurrent requests wait for the lock instead of hitting the database

5️⃣ All next reads are served directly from Redis — O(1) via Hash fields

---

## Customization

Each generated entity can override:

- `query()` → custom scopes, eager loading, column selection
- `hydrateKey()` → which field becomes the Hash key (default: primary key)
- `hydrateValue()` → what gets stored per record (default: `toArray()`)

---

## Design Patterns Used

### Template Method
- `Support/BaseRedisEntity.php` holds the shared algorithm
- Generated entities only define `$entity` and optionally `$model`

### Read-Through Cache
- Cache miss → fetch from DB → populate Redis → return data

### Contract-Driven Design
- `Contracts/RedisEntityInterface.php` defines the public API

🟡 Entities never talk to the database directly — the base class decides when to warm the cache.

🟡 The lock always runs on the Redis store, independent of your app's default cache driver.

---

## Why Redis Hashes?

- `HGETALL` for bulk reads — no `KEYS` / `SCAN`
- `HGET` for O(1) single-record access
- One `DEL` invalidates the whole entity

---

Implemented by Hesam Kazembeygi

📌 [My Website](https://kazembeygi.com)

📌 [My LinkedIn](https://www.linkedin.com/in/hessam-kazembeygi)

---

## License
Laravel Redis Entity is open-sourced software licensed under the [MIT license](https://opensource.org/license/MIT).