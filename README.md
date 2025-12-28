# avik/ignite

Ignite is the application bootstrap and assembly layer of the Avik framework.

It is responsible for preparing the application environment before
any HTTP request, CLI command, or job is executed.

---

## Purpose

Ignite assembles an Avik application by:

- Loading environment variables
- Loading configuration files
- Initializing the dependency container
- Registering service providers
- Booting service providers

Ignite does NOT handle routing, controllers, or HTTP execution.

---

## Typical Usage

```php
use Avik\Ignite\Application;
use Avik\Ignite\Bootstrap\{
    LoadEnvironment,
    LoadConfiguration,
    RegisterProviders,
    BootProviders
};

$app = new Application(__DIR__);

(new LoadEnvironment())->bootstrap($app);
(new LoadConfiguration())->bootstrap($app);

(new RegisterProviders())->bootstrap($app, [
    FlowServiceProvider::class,
    PathServiceProvider::class,
    CanvasServiceProvider::class,
]);

(new BootProviders())->bootstrap($app);
