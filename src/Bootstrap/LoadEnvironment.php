<?php

declare(strict_types=1);

namespace Avik\Ignite\Bootstrap;

use Avik\Essence\Env\Env;
use Avik\Ignite\Application;

final class LoadEnvironment
{
    public function bootstrap(Application $app): void
    {
        $envPath = $app->basePath('.env');

        if (is_file($envPath)) {
            Env::load($envPath);
        }

        $env = Env::get('APP_ENV', 'production');
        $app->instance('env', $env);
    }
}
