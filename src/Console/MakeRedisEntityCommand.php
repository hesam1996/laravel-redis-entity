<?php

declare(strict_types=1);

namespace Hesam1996\RedisEntity\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'redis-entity:make')]
class MakeRedisEntityCommand extends GeneratorCommand
{
    protected $name = 'redis-entity:make';

    protected $description = 'Create a new Redis entity service class';

    protected $type = 'Redis entity';

    protected function getStub(): string
    {
        // Pick the model-aware stub when --model is provided.
        $stub = $this->option('model')
            ? 'redis-entity.model.stub'
            : 'redis-entity.stub';

        return $this->resolveStubPath($stub);
    }

    /**
     * Allow the package consumer to override the stub.
     */
    protected function resolveStubPath(string $stub): string
    {
        $customPath = base_path("stubs/{$stub}");

        return file_exists($customPath)
            ? $customPath
            : __DIR__ . "/../stubs/{$stub}";
    }

    /**
     * products -> ProductRedisEntity
     */
    protected function getNameInput(): string
    {
        $name = trim($this->argument('name'));

        return Str::of($name)
            ->singular()
            ->studly()
            ->append('RedisEntity')
            ->toString();
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return config('redis-entity.namespace');
    }

    protected function getPath($name): string
    {
        return config('redis-entity.path')
            . '/' . $this->getNameInput() . '.php';
    }

    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

        $stub = str_replace('{{ entity }}', $this->entityName(), $stub);

        if ($model = $this->option('model')) {
            // Product -> App\Models\Product (respects the app's model namespace)
            $namespacedModel = $this->qualifyModel($model);

            $stub = str_replace(
                ['{{ namespacedModel }}', '{{ model }}'],
                [$namespacedModel, class_basename($namespacedModel)],
                $stub
            );
        }

        return $stub;
    }

    /**
     * products or Product -> products (the Redis key name)
     */
    protected function entityName(): string
    {
        return Str::of(trim($this->argument('name')))
            ->snake()
            ->plural()
            ->toString();
    }

    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Overwrite the class if it already exists'],
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The Eloquent model used as the fallback data source'],
        ];
    }
}
