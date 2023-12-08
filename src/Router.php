<?php

namespace src;

use src\Controllers\AppController;
use src\Helpers\ControllerMapper;
use src\Helpers\RouteResolver;

class Router
{
    private array $routes = [];
    private ControllerMapper $controllerMapper;
    private RouteResolver $routeResolver;

    public function __construct()
    {
        $this->controllerMapper = new ControllerMapper();
        $this->routeResolver = new RouteResolver();
    }

    public function run($url): void
    {
        $route = $this->matchRoute($url ?: 'index');

        if ($route === null) {
            die("Wrong url!");
        }

        // Extract parameters from the URL based on the dynamic parts
        $params = $this->extractDynamicParameters($url, $route->getPath());

        // Call the controller's action method with parameters
        call_user_func_array([new ($route->getController()), $route->getAction()], $params);
    }

    public function mapControllers(): void
    {
        $this->routes = $this->controllerMapper->mapControllers();
    }

    private function matchRoute(string $url): ?Route
    {
        foreach ($this->routes as $route) {
            if (!$this->routeResolver->checkAuthorization($route))
                continue;
            
            if (!$this->routeResolver->matchHttpMethod($route))
                continue;

            if (!$this->routeResolver->matchUrlParts($url, $route))
                continue;

            return $route;
        }

        return null;
    }

    private function extractDynamicParameters($url, $route): array
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
