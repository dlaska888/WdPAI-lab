<?php

declare(strict_types=1);
spl_autoload_extensions(".php"); // comma-separated list
spl_autoload_register(function ($class){
    $file = str_replace(["\\\\", "\\"], "/", $class) . ".php";
    require_once $file;
});

const DEBUG = true;
error_reporting(E_ALL);
ini_set('display_errors', DEBUG ? '1' : '0');

use src\Handlers\UserSessionHandler;
use src\Middlewares\ErrorHandlingMiddleware;
use src\LinkyRouting\RouterBuilder;

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

