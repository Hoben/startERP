<?php
// bootstrap.php

namespace App;

use PDO;
use DI\Container;
use PDOException;
use Monolog\Logger;
use Slim\Views\Twig;
use Twig\Environment;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;
use Twig\Loader\FilesystemLoader;

use Monolog\Handler\StreamHandler;
use App\Controller\DashboardController;


require __DIR__ . '/../vendor/autoload.php';

$container = new Container();

// Register Config
$container->set('config', function () {
    $config = new Config(); // Load .env from the root folder, customize if needed
    return $config;
});

// Register Twig
$container->set('twig', function ($c) {

    $loader = new FilesystemLoader(__DIR__ . '/../views');
    $config = $c->get('config');
    $twig = new Environment($loader, [
        'debug' => $config->debug,
        'cache' => $config->twigCache,
    ]);

    $twig->addExtension(new \Twig\Extension\DebugExtension());

    return $twig;
});

$container->set(LoggerInterface::class, function () {
    $logger = new Logger('app');
    $logger->pushHandler(new StreamHandler(__DIR__ . '/logs/app.log', Logger::DEBUG));
    return $logger;
});

$container->set(PDO::class, function ($c) {
    $config = $c->get('config');
    try {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=test;charset=utf8mb4', // e.g. 'mysql:host=localhost;dbname=mydb'
            'root',
            ''
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
});

/* Register Controllers */

$container->set(\App\Controller\AuthController::class, function ($c) {
    return new \App\Controller\AuthController(
        $c->get('twig'),
        $c->get('config'),
        $c->get(LoggerInterface::class),
        $c->get(PDO::class)
    );
});

$container->set(DashboardController::class, function ($c) {
    return new DashboardController(
        $c->get('twig'),
        $c->get('config'),
        $c->get(LoggerInterface::class),
        $c->get(PDO::class)
    );
});

return $container;
