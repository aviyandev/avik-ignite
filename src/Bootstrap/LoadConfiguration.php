<?php

declare(strict_types=1);

namespace Avik\Ignite\Bootstrap;

use Avik\Essence\Config\Repository;
use Avik\Essence\Config\Config;
use Avik\Ignite\Application;

final class LoadConfiguration
{
    public function bootstrap(Application $app): void
    {
        $configPath = $app->basePath('config');
        $items = [];

        if (is_dir($configPath)) {
            foreach (glob($configPath . '/*.php') as $file) {
                $key = basename($file, '.php');
                $items[$key] = require $file;
            }
        }

        $repository = new Repository($items);

        Config::setRepository($repository);
        $app->container()->instance(Repository::class, $repository);
    }
}
