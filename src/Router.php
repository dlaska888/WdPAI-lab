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
            $this->renderError(HttpStatusCode::INTERNAL_SERVER_ERROR, "Something went wrong");
        }
    }

    public function mapControllers(): void
    {
        $this->routes = $this->controllerMapper->mapControllers();
    }

    private function matchRoute(string $url): Route
    {
        $matchedRoutes = array_filter($this->routes, function (Route $route) use ($url) {
            return $this->routeResolver->matchUrlParts($url, $route);
        });

        if (empty($matchedRoutes)) {
            $this->renderError(HttpStatusCode::NOT_FOUND, 'Not found');
        }

        $matchedMethod = current(array_filter($matchedRoutes, function (Route $route) {
            return $this->routeResolver->matchHttpMethod($route);
        }));

        if (!$matchedMethod) {
            $this->renderError(HttpStatusCode::METHOD_NOT_ALLOWED, 'Method not allowed');
        }

        if (!$this->routeResolver->checkAuthorization($matchedMethod)) {
            $this->renderError(HttpStatusCode::UNAUTHORIZED, "You don't have access to this resource");
        }

        return $matchedMethod;
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

    private function renderError(HttpStatusCode $code, string $description): void
    {
        $this->appController->render( 'error', [
            'code' => $code,
            'description' => $description
        ], $code);
    }
}
