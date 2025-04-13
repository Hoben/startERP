<?php
// src/Controller/AuthController.php

namespace App\Controller;

use Slim\Psr7\Response;
use App\Core\Controller\BaseController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends BaseController
{
    // GET /login
    public function showLogin(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
    
        if ($this->isLoggedIn()) {
            // Already logged in, redirect to dashboard
            return $response
                ->withHeader('Location', '/dashboard')
                ->withStatus(302);
        }

        $queryParams  = $request->getQueryParams();
        $errorMessage = '';

        if (isset($queryParams['error']) && $queryParams['error'] === 'unauthorized') {
            $errorMessage = 'You must be logged in to access that page.';
        }

        $html = $this->twig->render('login.html.twig', [
            'error' => $errorMessage,
        ]);

        $response->getBody()->write($html);
        return $response;
    }

    // POST /login
    // This method is protected by the LoginRateLimiterMiddleware
    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
    
        if ($this->isLoggedIn()) {
            // Already logged in, redirect to dashboard
            return $response
                ->withHeader('Location', '/dashboard')
                ->withStatus(302);
        }

        $data = $request->getParsedBody();

        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;
        $rememberMe = $data['remember_me'] ?? null;

        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {

            $this->logger->warning('Failed login attempt', ['username' => $username]);

            $response = new Response();

            $errorMessage = 'Données saisies incorrectes.';

            $html = $this->twig->render('login.html.twig', [
                'error' => $errorMessage,
            ]);

            $response->getBody()->write($html);
            return $response;
        }

        $_SESSION['user_id'] = $user['id']; // or another identifier
        $_SESSION['username'] = $user['username'];

        // Optional: set longer session lifetime if remember me is checked
        if ($rememberMe) {
            // You can implement token-based "remember me" with cookies
            // This is just a basic placeholder for longer sessions
            ini_set('session.gc_maxlifetime', 604800); // 1 week
            setcookie(session_name(), session_id(), time() + 604800, "/");
        }

        $this->logger->info('User logged in', ['user_id' => $user['id']]);

        // 🔁 Redirect to dashboard or home
        return $response
            ->withHeader('Location', '/dashboard')
            ->withStatus(302);
    }

    // ANY /logout
    // This method is protected by the AuthMiddleware
    public function logout(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        // Clear all session data
        $_SESSION = [];

        // Destroy the sessionp
        session_destroy();

        // Optional: unset the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Redirect to login page or home
        return $response
            ->withHeader('Location', '/login')
            ->withStatus(302);
    }
}
