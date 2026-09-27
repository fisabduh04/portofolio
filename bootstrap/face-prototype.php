<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Configuration\ApplicationBuilder;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Support\ServiceProvider;

/** This entry point deliberately does not load the school's routes or providers. */
foreach (['APP_ENV' => 'testing', 'APP_CONFIG_CACHE' => 'bootstrap/face-prototype.disabled',
    'APP_ROUTES_CACHE' => 'bootstrap/face-prototype-routes.disabled',
    'APP_SERVICES_CACHE' => 'storage/framework/cache/face-prototype-services.php',
] as $key => $value) {
    putenv($key.'='.$value);
    $_ENV[$key] = $_SERVER[$key] = $value;
}
if (is_file(__DIR__.'/face-prototype.disabled') || is_file(__DIR__.'/face-prototype-routes.disabled')) {
    throw new RuntimeException('Prototype must run without cached application configuration or routes.');
}

$app = (new ApplicationBuilder(new Application(dirname(__DIR__))))
    ->withKernels()
    ->withProviders([], withBootstrapProviders: false)
    ->withRouting(using: function (): void {
        require __DIR__.'/../routes/face-prototype.php';
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(\App\Http\Middleware\EnsureFacePrototype::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash(['descriptor', 'references', 'token']);
        $exceptions->shouldRenderJsonWhen(fn (): bool => true);
    })->create();

$manifest = new PackageManifest(new Filesystem, dirname(__DIR__), '');
$manifest->manifest = [];
$app->instance(PackageManifest::class, $manifest);
$app->useEnvironmentPath(__DIR__);
$app->loadEnvironmentFrom('face-prototype.env');
$app->afterBootstrapping(LoadConfiguration::class, function (Application $app): void {
    $app['config']->set([
        'app.env' => 'testing', 'app.debug' => false, 'app.timezone' => 'Asia/Jakarta',
        'app.providers' => ServiceProvider::defaultProviders()->toArray(),
        'database.default' => 'sqlite',
        'database.connections' => ['sqlite' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true]],
        'cache.default' => 'array', 'session.driver' => 'array', 'queue.default' => 'null',
        'logging.default' => 'null', 'mail.default' => 'array',
        'face-prototype.isolated' => true,
    ]);
});

return $app;
