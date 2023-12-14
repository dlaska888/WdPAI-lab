<?php

declare(strict_types=1);
spl_autoload_extensions(".php"); // comma-separated list
spl_autoload_register();

const DEBUG = true;
error_reporting(E_ALL);
ini_set('display_errors', DEBUG ? '1' : '0');

use src\Router;

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

$router = new Router();
$router->mapControllers();
$router->run($path);

