<?php

declare(strict_types=1);
spl_autoload_extensions(".php"); // comma-separated list
spl_autoload_register();

const DEBUG = true;
error_reporting(E_ALL);
ini_set('display_errors', DEBUG ? '1' : '0');

use src\Handlers\UserSessionHandler;
use src\middlewares\ErrorHandlingMiddleware;
use src\LinkyRouting\RouterBuilder;

$builder = new RouterBuilder();

$builder->setViewsPath('src/views');
$builder->setControllersPath('src/controllers');

$builder->setSessionHandler(new UserSessionHandler());
$builder->useAuthorization();

$builder->addMiddleware(new ErrorHandlingMiddleware());
$builder->mapControllers();

$router = $builder->build();
$router->run();

