<?php

namespace src\routing;

use src\routing\enums\HttpStatusCode;
use src\routing\helpers\HttpResponseHandler;
use src\routing\helpers\RouteResolver;
use src\routing\middleware\interfaces\IMiddleware;
use src\routing\responses\ErrorView;
use src\routing\responses\Json;
use src\routing\responses\Response;
use src\routing\responses\View;

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

        $this->generateResponse($response);
    }

    private function matchRoute(string $url): Route
    {
        $matchedByPath = array_filter($this->routes, function (Route $route) use ($url) {
            return $this->routeResolver->matchUrlParts($url, $route);
        });

        if (empty($matchedByPath)) {
            $this->responseHandler->view(new ErrorView(HttpStatusCode::NOT_FOUND, "Not found"));
        }

        $matchedByMethod = current(array_filter($matchedByPath, function (Route $route) {
            return $this->routeResolver->matchHttpMethod($route);
        }));

        if (!$matchedByMethod) {
            $this->responseHandler->view(new ErrorView(HttpStatusCode::METHOD_NOT_ALLOWED, "Method not allowed"));
        }

        return $matchedByMethod;
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

    private function generateResponse(Response $response): void
    {
        if($response instanceof View){
            $this->responseHandler->view($response);
        }
        elseif ($response instanceof Json){
            $this->responseHandler->json($response);
        }
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
