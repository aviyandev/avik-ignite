<?php

declare(strict_types=1);

namespace Avik\Ignite;

use Avik\Crate\Container;

final class Application
{
    private Container $container;
    private array $providers = [];

    public function __construct(
        private string $basePath
    ) {
        $this->container = new Container();

        // self bindings
        $this->container->instance(self::class, $this);
        $this->container->instance(Container::class, $this->container);
    }

    public function basePath(string $path = ''): string
    {
        return rtrim($this->basePath . '/' . ltrim($path, '/'), '/');
    }

    public function container(): Container
    {
        return $this->container;
    }

    public function register(string $provider): void
    {
        $instance = new $provider($this);
        $instance->register();
        $this->providers[] = $instance;
    }

    public function boot(): void
    {
        foreach ($this->providers as $provider) {
            $provider->boot();
        }
    }
}
