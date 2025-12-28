<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Avik\Ignite\Application;
use Avik\Crate\Container;
use Avik\Ignite\Bootstrap\LoadEnvironment;
use Avik\Ignite\Bootstrap\LoadConfiguration;
use Avik\Ignite\Bootstrap\RegisterProviders;
use Avik\Ignite\Bootstrap\BootProviders;

function assert_true($condition, $message)
{
    if (!$condition) {
        throw new Exception("Assertion failed: $message");
    }
    echo "✅ $message\n";
}

try {
    echo "Starting Application Tests...\n\n";

    $app = new Application(__DIR__);

    // Test Instantiation
    assert_true($app instanceof Application, "Application is instantiated correctly");
    assert_true($app->container() instanceof Container, "Container is initialized");
    assert_true($app->basePath() === __DIR__, "Base path is correct");
    assert_true($app->basePath('config') === __DIR__ . DIRECTORY_SEPARATOR . 'config', "Sub-path resolution works");

    // Test Container Proxies
    $app->instance('foo', 'bar');
    assert_true($app->make('foo') === 'bar', "Container instance proxy works");

    $app->singleton('singleton', function () {
        return new stdClass();
    });
    $s1 = $app->make('singleton');
    $s2 = $app->make('singleton');
    assert_true($s1 === $s2, "Container singleton proxy works");

    // Test Environment Detection (Default)
    assert_true($app->isProduction(), "Default environment is production");
    assert_true(!$app->isLocal(), "Default is not local");

    // Test Manual Env Override
    $app->instance('env', 'local');
    assert_true($app->isLocal(), "Manual environment override works");
    assert_true($app->environment() === 'local', "environment() returns correct value");

    // Test Termination Logic
    $terminated = false;
    $app->terminating(function () use (&$terminated) {
        $terminated = true;
    });
    $app->terminate();
    assert_true($terminated === true, "Termination callbacks are executed");

    // Test Bootstrap Lifecycle
    echo "\nTesting Bootstrap Lifecycle...\n";

    // Create a mock config directory
    $configDir = __DIR__ . DIRECTORY_SEPARATOR . 'config';
    if (!is_dir($configDir)) {
        mkdir($configDir, 0777, true);
    }

    $configContent = "<?php\nreturn ['providers' => ['" . \Avik\Ignite\IgniteServiceProvider::class . "']];";
    file_put_contents($configDir . DIRECTORY_SEPARATOR . 'app.php', $configContent);

    // Verify file exists and content is correct
    if (!file_exists($configDir . DIRECTORY_SEPARATOR . 'app.php')) {
        throw new Exception("Failed to create mock config file");
    }

    $app = new Application(__DIR__);

    $app->bootstrapWith([
        LoadConfiguration::class,
        RegisterProviders::class,
        BootProviders::class,
    ]);

    $repository = $app->make(\Avik\Essence\Config\Repository::class);
    $allConfig = $repository->all();
    $providers = $allConfig['app']['providers'] ?? null;
    assert_true($providers !== null, "Configuration was loaded into repository");
    assert_true(count($providers) === 1, "Providers were registered from config");

    // Cleanup mock config
    unlink($configDir . DIRECTORY_SEPARATOR . 'app.php');
    rmdir($configDir);

    echo "\nAll tests passed successfully! 🚀\n";
} catch (Exception $e) {
    echo "\n❌ Test Failed: " . $e->getMessage() . "\n";
    exit(1);
}
