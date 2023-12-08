<?php

namespace src;

use src\Attributes\ApiController;
use src\Attributes\httpMethod\HttpMethod;
use src\Attributes\MvcController;
use src\Attributes\Route;
use ReflectionClass;

class Router
{
    public static array $routes = [];

    public static function run($url): void
    {
        $route = self::matchRoute($url ?: 'index');

        if ($route === null) {
            die("Wrong url!");
        }

        $action = self::$routes[$route];

        // Extract parameters from the URL based on the dynamic parts
        $params = self::extractDynamicParameters($url, $route);

        // Call the controller's action method with parameters
        call_user_func_array([new $action['controller'], $action['action']], $params);
    }

    public static function mapControllers(): void
    {
        // Specify the directory where your controllers are located
        $controllersDirectory = 'src/controllers';

        // Get all PHP files in the controllers directory
        $phpFiles = glob($controllersDirectory . '/*.php');

        foreach ($phpFiles as $phpFile) {
            $className = "src\\Controllers\\" . pathinfo($phpFile, PATHINFO_FILENAME);
            if (!class_exists($className)) {
                include $phpFile;
            }

            $reflectionClass = new ReflectionClass($className);
            $attributes = $reflectionClass->getAttributes(ApiController::class) ?:
                $reflectionClass->getAttributes(MvcController::class);

            if (!empty($attributes)) {
                self::mapRoutes($className);
            }

        }
    }

    private static function mapRoutes($controllerClass): void
    {
        $reflection = new ReflectionClass($controllerClass);

        foreach ($reflection->getMethods() as $method) {
            $methodName = $method->getName();
            $routeAttributes = $method->getAttributes(Route::class);
            $httpMethodAttributes = array_filter($method->getAttributes(),
                fn($attribute) => is_subclass_of($attribute->getName(), HttpMethod::class)
            );

            if (empty($routeAttributes)) {
                continue;
            }

            $route = $routeAttributes[0]->getArguments()[0] ?? $methodName;
            $httpMethods = array();

            foreach ($httpMethodAttributes as $httpMethodAttribute) {
                $httpMethodAttribute = $httpMethodAttribute->newInstance();
                $httpMethods[] = $httpMethodAttribute->method;
            }

            if (empty($httpMethods)) {
                $httpMethods[] = 'GET';
            }

            foreach ($httpMethods as $httpMethod) {
                self::$routes[$httpMethod . "::" . $route] = ['controller' => $controllerClass,
                    'action' => $methodName];
            }


        }
    }

    private static function matchRoute($url): ?string
    {
        $urlParts = explode("/", $url ?: 'index');

        foreach (self::$routes as $route => $action) {
            list($httpMethod, $route) = explode("::", $route);
            $routeParts = explode("/", $route);

            if ($_SERVER['REQUEST_METHOD'] !== $httpMethod) {
                continue;
            }

            $match = true;
            
            foreach ($routeParts as $index => $part) {
                if ((!isset($urlParts[$index]) || $part !== $urlParts[$index]) && !str_starts_with($part, '{')
                ) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                return implode("::", [$httpMethod, $route]);
            }
        }

        return null;
    }

    private static function extractDynamicParameters($url, $route): array
    {
        $urlParts = explode("/", $url ?: "index");
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
