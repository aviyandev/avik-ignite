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
            $iterator = new \DirectoryIterator($configPath);
            foreach ($iterator as $fileinfo) {
                if (!$fileinfo->isDot() && $fileinfo->getExtension() === 'php') {
                    $key = $fileinfo->getBasename('.php');
                    $value = require $fileinfo->getPathname();

                    if (is_array($value)) {
                        $items[$key] = $value;
                    }
                }
            }
        }

        $repository = new Repository($items);

        Config::setRepository($repository);
        $app->container()->instance(Repository::class, $repository);
    }
}
