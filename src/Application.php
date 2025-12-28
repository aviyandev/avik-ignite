<?php

declare(strict_types=1);

namespace Avik\Ignite;

use Avik\Crate\Container;

final class Application
{
    private Container $container;
    private array $providers = [];
    private array $terminatingCallbacks = [];
    private bool $booted = false;

    public function __construct(
        private string $basePath
    ) {
        $this->container = new Container();

        // self bindings
        $this->container->instance(self::class, $this);
        $this->container->instance(Container::class, $this->container);
    }

    public function bootstrapWith(array $bootstrappers): void
    {
        foreach ($bootstrappers as $bootstrapper) {
            (new $bootstrapper())->bootstrap($this);
        }
    }

    public function environment(): string
    {
        return $this->container->has('env') ? $this->container->make('env') : 'production';
    }

    public function isLocal(): bool
    {
        return $this->environment() === 'local';
    }

    public function isProduction(): bool
    {
        return $this->environment() === 'production';
    }

    public function isTesting(): bool
    {
        return $this->environment() === 'testing';
    }

    public function basePath(string $path = ''): string
    {
        return rtrim($this->basePath . DIRECTORY_SEPARATOR . ltrim($path, '/\\'), DIRECTORY_SEPARATOR);
    }

    public function container(): Container
    {
        return $this->container;
    }

    public function singleton(string $abstract, mixed $concrete = null): void
    {
        $this->container->singleton($abstract, $concrete);
    }

    public function bind(string $abstract, mixed $concrete = null, bool $shared = false): void
    {
        $this->container->bind($abstract, $concrete, $shared);
    }

    public function instance(string $abstract, mixed $instance): void
    {
        $this->container->instance($abstract, $instance);
    }

    public function make(string $abstract, array $parameters = []): mixed
    {
        return $this->container->make($abstract, $parameters);
    }

    public function register(string $provider): void
    {
        $instance = new $provider($this);
        $instance->register();
        $this->providers[] = $instance;
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        foreach ($this->providers as $provider) {
            $provider->boot();
        }

        $this->booted = true;
    }

    public function terminating(callable $callback): void
    {
        $this->terminatingCallbacks[] = $callback;
    }

    public function terminate(): void
    {
        foreach ($this->terminatingCallbacks as $callback) {
            $callback();
        }
    }
}
