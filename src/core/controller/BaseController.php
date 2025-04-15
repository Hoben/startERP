<?php
// src/Controller/BaseController.php

namespace App\Core\Controller;

use Twig\Environment as Twig;
use App\Config;
use Psr\Log\LoggerInterface;
use PDO;

abstract class BaseController
{
    protected Twig $twig;
    protected Config $config;
    protected LoggerInterface $logger;
    protected PDO $db;

    public function __construct(
        Twig $twig,
        Config $config,
        LoggerInterface $logger,
        PDO $db
    ) {
        $this->twig = $twig;
        $this->config = $config;
        $this->logger = $logger;
        $this->db = $db;
    }

    public function isLoggedIn(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public function getLoggedUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public function getLoggedUser(): \App\Model\User
    {
        $userRepository = new \App\Repository\UserRepository($this->db);
        $userId = $this->getLoggedUserId();

        if ($userId === null) {
            throw new \Exception('User not logged in');
        }
        
        $user = $userRepository->findById($userId);
        return $user;
    }
}
