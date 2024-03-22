<?php

declare(strict_types=1);

use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

require __DIR__ . '/vendor/autoload.php';
//spl_autoload_extensions(".php"); // comma-separated list
//spl_autoload_register(function ($class){
//    $file = str_replace(["\\\\", "\\"], "/", $class) . ".php";
//    require_once $file;
//});

//const DEBUG = true;
//error_reporting(E_ALL);
//ini_set('display_errors', DEBUG ? '1' : '0');
//
//use vendor\Facebook\WebDriver\Chrome\ChromeOptions;
//use Facebook\WebDriver\Remote\DesiredCapabilities;
//use Facebook\WebDriver\Remote\RemoteWebDriver;
//use src\Handlers\UserSessionHandler;
//use src\Middlewares\ErrorHandlingMiddleware;
//use src\LinkyRouting\RouterBuilder;
//
//$builder = new RouterBuilder();
//
//header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Headers: *');
//
//$builder->setViewsPath('src/Views');
//$builder->setControllersPath('src/Controllers');
//
//$builder->setSessionHandler(new UserSessionHandler());
//$builder->useAuthorization();
//
//$builder->addMiddleware(new ErrorHandlingMiddleware());
//$builder->mapControllers();
//
//$router = $builder->build();
//$router->run();

$serverUrl = 'http://selenium:4444';

$desiredCapabilities = DesiredCapabilities::firefox();

// Disable accepting SSL certificates
$desiredCapabilities->setCapability('acceptSslCerts', false);

// Add arguments via FirefoxOptions to start headless firefox
$chromeOptions = new FirefoxOptions();
$chromeOptions->addArguments(['-headless']);
$desiredCapabilities->setCapability(FirefoxOptions::CAPABILITY, $chromeOptions);

$driver = RemoteWebDriver::create($serverUrl, $desiredCapabilities);
$driver->get("https://youtube.com");
echo $driver->getTitle();
