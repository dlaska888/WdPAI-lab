<?php

namespace src;

use src\Controllers\AppController;
use src\Enums\HttpStatusCode;
use src\Helpers\ControllerMapper;
use src\Helpers\RouteResolver;
use Throwable;

class Router
{
    private array $routes = [];
    private ControllerMapper $controllerMapper;
    private RouteResolver $routeResolver;
    private AppController $appController;

    public function __construct()
    {
        $this->controllerMapper = new ControllerMapper();
        $this->routeResolver = new RouteResolver();
        $this->appController = new AppController();
    }

    public function run($url): void
    {
        $route = $this->matchRoute($url ?: 'index');

        // Extract parameters from the URL based on the dynamic parts
        $params = $this->extractDynamicParameters($url, $route->getPath());

        // Call the controller's action method with parameters
        try {
            call_user_func_array([new ($route->getController()), $route->getAction()], $params);
        }
        catch (Throwable) {
            $this->appController->render('error',
                ['code' => HttpStatusCode::INTERNAL_SERVER_ERROR, 'description' => 'Something went wrong']);
        }
    }

    public function mapControllers(): void
    {
        $this->routes = $this->controllerMapper->mapControllers();
    }

    private function matchRoute(string $url): Route
    {
        $found = null;
        foreach ($this->routes as $route) {
            if (!$this->routeResolver->matchHttpMethod($route)) {
                continue;
            }

            if (!$this->routeResolver->matchUrlParts($url, $route)) {
                continue;
            }

            $found = $route;
        }

        if ($found === null) {
            $this->appController->render('error',
                ['code' => HttpStatusCode::NOT_FOUND, 'description' => 'This page does not exist']);
        }

        if (!$this->routeResolver->checkAuthorization($found)) {
            $this->appController->render('error',
                ['code' => HttpStatusCode::UNAUTHORIZED, 'description' => "You don't have access to this resource"]);
        }

        return $found;
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
