<?php
// src/Middleware/AuthMiddleware.php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (empty($_SESSION['user_id'])) {
            $response = new Response();
            return $response
                ->withHeader('Location', '/login?error=unauthorized')
                ->withStatus(302);
        }

        return $handler->handle($request);
    }
}
