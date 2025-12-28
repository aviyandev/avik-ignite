<?php

declare(strict_types=1);

namespace Avik\Ignite\Bootstrap;

use Avik\Ignite\Application;

final class BootProviders
{
    public function bootstrap(Application $app): void
    {
        $app->boot();
    }
}
