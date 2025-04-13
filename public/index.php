<?php
use App\Middleware\AuthMiddleware;
use App\Middleware\LoginRateLimiterMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Whoops\Run;

use function DI\value;

require __DIR__ . '/../vendor/autoload.php';

// Import container
$container = require __DIR__ . '/../src/bootstrap.php';

$config = $container->get('config');

AppFactory::setContainer($container);
$app = AppFactory::create();

// Set base path correctly
$app->setBasePath($config->basePath);

// Add ErrorMiddleware for general error handling
$app->addErrorMiddleware($config->debug, true, true); // Enable detailed errors in development

// Auto start session
// This middleware will start the session if it is not already started
$app->add(function ($request, $handler) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    return $handler->handle($request);
});


// Load routes
$webRoutes = require __DIR__ . '/../routes/web.php';
$apiRoutes = require __DIR__ . '/../routes/api.php';
$webRoutes($app);
$apiRoutes($app);

$app->run();
