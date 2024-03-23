<?php

declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';
error_reporting(E_ALL);

use LinkyApp\Handlers\UserSessionHandler;
use LinkyApp\LinkyRouting\RouterBuilder;
use LinkyApp\Middlewares\ErrorHandlingMiddleware;

$builder = new RouterBuilder();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');

$builder->setViewsPath('src/Views');
$builder->setControllersPath('src/Controllers');

$builder->setSessionHandler(new UserSessionHandler());
$builder->useAuthorization();

$builder->addMiddleware(new ErrorHandlingMiddleware());
$builder->mapControllers();

$router = $builder->build();
$router->run();

