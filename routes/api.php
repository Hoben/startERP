<?php
// routes/api.php

use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use App\Middleware\AuthMiddleware;
use App\Repository\UserRepository;
use App\Repository\OrganisationRepository;
use App\Middleware\LoginRateLimiterMiddleware;

return function (App $app) {

    $container = $app->getContainer();

    $app->get('/api/v1/organisations', function (Request $request, Response $response) use ($container) {
        // Fetch all organisations from the database
        $organisationRepository = new OrganisationRepository($container->get(PDO::class));
        $organisations = $organisationRepository->findAll();

        // Convert to JSON and return
        $response->getBody()->write(json_encode($organisations));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new AuthMiddleware());

    $app->get('/api/v1/organisations/{id}', function (Request $request, Response $response, array $args)  use ($container) {
        // Fetch organisation by ID from the database
        $organisationRepository = new OrganisationRepository($container->get(PDO::class));
        $organisation = $organisationRepository->findById((int)$args['id']);

        if (!$organisation) {
            $response->getBody()->write('Organisation not found');
            return $response->withStatus(404);
        }

        // Convert to JSON and return
        $response->getBody()->write(json_encode($organisation));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new AuthMiddleware());

    $app->get('/api/v1/users', function (Request $request, Response $response)  use ($container) {
        // Fetch all users from the database
        $userRepository = new UserRepository($container->get(PDO::class));
        $users = $userRepository->findAll();

        // Convert to JSON and return
        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new AuthMiddleware());

    $app->get('/api/v1/users/{id}', function (Request $request, Response $response, array $args)  use ($container) {
        // Fetch user by ID from the database
        $userRepository = new UserRepository($container->get(PDO::class));
        $user = $userRepository->findById((int)$args['id']);

        if (!$user) {
            $response->getBody()->write('User not found');
            return $response->withStatus(404);
        }

        // Convert to JSON and return
        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new AuthMiddleware());


};
