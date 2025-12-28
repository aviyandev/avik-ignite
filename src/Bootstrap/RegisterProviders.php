<?php

declare(strict_types=1);

namespace Avik\Ignite\Bootstrap;

use Avik\Ignite\Application;

final class RegisterProviders
{
    public function bootstrap(Application $app, array $providers): void
    {
        foreach ($providers as $provider) {
            $app->register($provider);
        }
    }
}
