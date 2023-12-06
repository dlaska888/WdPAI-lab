<?php

namespace src;

use src\Attributes\ApiController;
use src\Attributes\Route;
use ReflectionClass;
use src\Controllers;

class Router
{
    public static array $routes = [];

    public static function run($url): void
    {
        $route = self::matchRoute($url);

        if ($route === null) {
            die("Wrong url!");
        }

        $controllerAction = self::$routes[$route];

        // Extract parameters from the URL based on the dynamic parts
        $params = self::extractDynamicParameters($url, $route);

        // Call the controller's action method with parameters
        call_user_func_array([new $controllerAction['controller'], $controllerAction['action']], $params);
    }

    public static function mapControllers(): void
    {
        // Specify the directory where your controllers are located
        $controllersDirectory = __DIR__ . '/controllers';

        // Get all PHP files in the controllers directory
        $phpFiles = glob($controllersDirectory . '/*.php');

        foreach ($phpFiles as $phpFile) {
            $className = "src\\Controllers\\" . pathinfo($phpFile, PATHINFO_FILENAME);
            if(!class_exists($className))
                include $phpFile;
            
            $reflectionClass = new ReflectionClass($className);
            $attributes = $reflectionClass->getAttributes(ApiController::class);

            if (!empty($attributes)) {
                self::mapRoutes($className);
            }
        }
    }

    public static function mapRoutes($controllerClass): void
    {
        $reflection = new ReflectionClass($controllerClass);

        foreach ($reflection->getMethods() as $method) {
            $methodName = $method->getName();
            $attributes = $method->getAttributes(Route::class);

            if (!empty($attributes)) {
                $route = $attributes[0]->getArguments()[0];
                self::$routes[$route] = ['controller' => $controllerClass, 'action' => $methodName];
            }
        }
    }

    private static function matchRoute($url): ?string
    {
        $urlParts = explode("/", $url);

        foreach (self::$routes as $route => $controllerAction) {
            $routeParts = explode("/", $route);
            $match = true;

            foreach ($routeParts as $index => $part) {
                if ((!isset($urlParts[$index]) || $part !== $urlParts[$index]) &&
                    !str_starts_with($part, '{')
                ) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                return $route;
            }
        }

        return null;
    }

    private static function extractDynamicParameters($url, $route): array
    {
        $urlParts = explode("/", $url);
        $routeParts = explode("/", $route);
        $params = [];

        foreach ($routeParts as $index => $part) {
            if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                // Dynamic part in the route, extract the value from the URL
                $paramKey = trim($part, '{}');
                $params[$paramKey] = $urlParts[$index];
            }
        }

        return $params;
    }
}
