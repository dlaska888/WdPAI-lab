<?php

declare(strict_types=1);
spl_autoload_extensions(".php"); // comma-separated list
spl_autoload_register();

use src\Router;

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

// Example usage with attributes:
Router::mapControllers();
Router::run($path);

