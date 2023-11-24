<?php

declare(strict_types=1);

require_once 'src/Router.php';

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

Router::get('', DefaultController::class);
Router::get('index', DefaultController::class);
Router::get('login', SecurityController::class);
Router::get('register', SecurityController::class);
Router::get('dashboard', DefaultController::class);

Router::run($path);

