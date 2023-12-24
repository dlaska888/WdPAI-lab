<?php

namespace src\LinkyRouting;

use src\LinkyRouting\attributes\controller\MvcController;
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

    public function run($url): void
    {
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
            $this->responseHandler->handleResponse(new Error(MvcController::class, "Page not found",
                "error", HttpStatusCode::NOT_FOUND));
        }

        $matchedByMethod = current(array_filter($matchedByPath, fn(Route $route) => $this->routeResolver->matchHttpMethod($route)));

        if (!$matchedByMethod) {
            $this->responseHandler->handleResponse(new Error(MvcController::class, "Method not allowed",
                "error", HttpStatusCode::METHOD_NOT_ALLOWED));
        }

        return $matchedByMethod;
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
