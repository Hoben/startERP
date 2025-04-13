<?php

namespace App;

use Dotenv\Dotenv;

class Config
{
    public bool $debug;
    public string $baseURL;
    public string $basePath;
    public bool $twigCache;

    public function __construct(string $envPath = __DIR__ . '/../')
    {
        // Load .env file from the specified path (default to the root folder)
        $dotenv = Dotenv::createImmutable($envPath);
        $dotenv->load();

        // Ensure all necessary environment variables are present
        $this->debug = filter_var($_ENV['DEBUG'] ?? null, FILTER_VALIDATE_BOOLEAN);
        if ($this->debug === null) {
            throw new \Exception('DEBUG is not set in the .env file');
        }

        $this->baseURL = $_ENV['BASE_URL'] ?? null;
        if ($this->baseURL === null) {
            throw new \Exception('BASE_URL is not set in the .env file');
        }

        $this->basePath = $_ENV['BASE_PATH'] ?? null;
        if ($this->basePath === null) {
            throw new \Exception('BASE_PATH is not set in the .env file');
        }

        $this->twigCache = filter_var($_ENV['TWIG_CACHE'] ?? null, FILTER_VALIDATE_BOOLEAN);
        if ($this->twigCache === null) {
            throw new \Exception('TWIG_CACHE is not set in the .env file');
        }
    }
}
