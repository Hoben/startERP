<?php
// src/Middleware/LoginRateLimiterMiddleware.php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

class LoginRateLimiterMiddleware implements MiddlewareInterface
{
    private RateLimiterFactory $ipLimiterFactory;
    private RateLimiterFactory $userLimiterFactory;

    public function __construct()
    {
        $storage = new InMemoryStorage();

        $this->ipLimiterFactory = new RateLimiterFactory([
            'id' => '',
            'policy' => 'sliding_window',
            'limit' => 10,
            'interval' => '1 minute',
        ], $storage);

        $this->userLimiterFactory = new RateLimiterFactory([
            'id' => '',
            'policy' => 'sliding_window',
            'limit' => 5,
            'interval' => '1 minute',
        ], $storage);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() !== 'POST') {
            return $handler->handle($request);
        }

        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? 'unknown';
        $params = (array) $request->getParsedBody();
        $username = $params['username'] ?? 'anonymous';

        $ipLimiter = $this->ipLimiterFactory->create($ip);
        if (!$ipLimiter->consume()->isAccepted()) {
            return $this->tooManyAttemptsResponse('IP');
        }

        $userLimiter = $this->userLimiterFactory->create($username);
        if (!$userLimiter->consume()->isAccepted()) {
            return $this->tooManyAttemptsResponse("user '$username'");
        }

        return $handler->handle($request);
    }

    private function tooManyAttemptsResponse(string $target): Response
    {
        $response = new Response();
        $response->getBody()->write("Too many login attempts for $target. Try again later.");
        return $response->withStatus(429);
    }
}
