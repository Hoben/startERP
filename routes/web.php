<?php
// routes/web.php

use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use App\Controller\AuthController;
use App\Controller\DashboardController;
use App\Middleware\AuthMiddleware;
use App\Middleware\LoginRateLimiterMiddleware;

return function (App $app) {

    $app->get('/login',     [AuthController::class, 'showLogin']);
    $app->post('/login',    [AuthController::class, 'login'])->add(new LoginRateLimiterMiddleware());
    $app->any('/logout',    [AuthController::class, 'logout'])->add(new AuthMiddleware());

    // All other routes require session auth
    $secureRoutes = $app->group('', function () use ($app) {
        $app->get('/dashboard', [DashboardController::class, 'show']);
    });
    
    $secureRoutes->add(new AuthMiddleware());
};
