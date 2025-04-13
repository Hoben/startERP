<?php
// src/Controller/AuthController.php

namespace App\Controller;

use Slim\Psr7\Response;
use App\Core\Controller\BaseController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DashboardController extends BaseController
{
    // GET /login
    public function show(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response = new Response();

        $html = $this->twig->render('dashboard.html.twig', [
            'user_id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null
        ]);

        $response->getBody()->write($html);

        return $response;
    }
}