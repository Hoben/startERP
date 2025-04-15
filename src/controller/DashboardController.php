<?php
// src/Controller/AuthController.php

namespace App\Controller;

use Slim\Psr7\Response;
use App\Repository\UserRepository;
use App\Core\Controller\BaseController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DashboardController extends BaseController
{
    protected UserRepository $userRepository;

    // GET /
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        // Redirect to login page if not logged in
        if (!$this->isLoggedIn()) {
            return $response
                ->withHeader('Location', '/login')
                ->withStatus(302);
        }

        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    // GET /dashboard
    public function show(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $user = $this->getLoggedUser();
        $response = new Response();

        $html = $this->twig->render('dashboard.html.twig', [
            'user' => $user,
        ]);

        $response->getBody()->write($html);

        return $response;
    }
}
