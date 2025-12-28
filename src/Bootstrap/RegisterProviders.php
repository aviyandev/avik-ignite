<?php

declare(strict_types=1);

namespace Avik\Ignite\Bootstrap;

use Avik\Essence\Config\Config;
use Avik\Ignite\Application;

final class RegisterProviders
{
    public function bootstrap(Application $app): void
    {
        $providers = Config::get('app.providers', []);

        foreach ($providers as $provider) {
            $app->register($provider);
        }
    }
}
