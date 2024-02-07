<?php

namespace src\LinkyRouting;

use src\LinkyRouting\enums\HttpStatusCode;
use src\LinkyRouting\helpers\HttpResponseHandler;
use src\LinkyRouting\helpers\RouteResolver;
use src\LinkyRouting\middleware\interfaces\IMiddleware;
use src\LinkyRouting\Responses\Error;

class Router
{
    private array $routes = [];
    private IMiddleware $middlewareChain;
    private RouteResolver $routeResolver;
    private HttpResponseHandler $responseHandler;

    public function __construct()
    {
        $this->routeResolver = new RouteResolver();
    }

    public function run(): void
    {
        $url = trim($_SERVER['REQUEST_URI'], '/');
        $url = parse_url($url, PHP_URL_PATH);
        
        $route = $this->matchRoute($url ?: 'index');
        $params = $this->extractDynamicParameters($url, $route->getPath());

        $request = new Request($route, $params);
        $response = $this->middlewareChain->invoke($request);

        $this->responseHandler->handleResponse($response);
    }

    private function matchRoute(string $url): Route
    {
        $matchedByPath = array_filter($this->routes, fn(Route $route) => $this->routeResolver->matchUrlParts($url, $route));

        if (empty($matchedByPath)) {
            $this->responseHandler->handleResponse(
                new Error(null, "Page not found", HttpStatusCode::NOT_FOUND)
            );
        }

        $matchedByMethod = $this->findByMethod($matchedByPath);

        if (empty($matchedByMethod)) {
            $this->responseHandler->handleResponse(
                new Error(null, "Method not allowed", HttpStatusCode::METHOD_NOT_ALLOWED)
            );
        }

        return $matchedByMethod;
    }

    private function findByMethod(array $routes) : ?Route
    {
        foreach ($routes as $route) {
            if ($this->routeResolver->matchHttpMethod($_SERVER["REQUEST_METHOD"], $route)) {
                return $route;
            }
        }

        return null;
    }

    private function extractDynamicParameters($url, $route): array
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

    public function setRoutes(array $routes): void
    {
        $this->routes = $routes;
    }

    public function setMiddlewareChain(IMiddleware $middlewareChain): void
    {
        $this->middlewareChain = $middlewareChain;
    }

    public function setResponseHandler(HttpResponseHandler $responseHandler): void
    {
        $this->responseHandler = $responseHandler;
    }

}
