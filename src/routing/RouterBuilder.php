<?php

namespace src\routing;

use src\routing\helpers\ControllerMapper;
use src\routing\helpers\HttpResponseHandler;
use src\routing\middleware\AuthorizationMiddleware;
use src\routing\middleware\interfaces\IMiddleware;
use src\routing\middleware\MiddlewareChain;

class RouterBuilder
{
    private array $routes = [];
    private MiddlewareChain $middlewareChain;
    private string $viewsPath;
    private string $controllersPath;

    public function __construct()
    {
        $this->middlewareChain = new MiddlewareChain();
    }

    public function build(): Router
    {
        $router = new Router();
        $router->setRoutes($this->routes);
        $router->setMiddlewareChain($this->middlewareChain);
        $router->setResponseHandler(new HttpResponseHandler($this->viewsPath ?? ""));
        $router->setResponseHandler(new HttpResponseHandler($this->viewsPath ?? ""));

        return $router;
    }

    public function addMiddleware(IMiddleware $middleware): void
    {
        $this->middlewareChain->add($middleware);
    }

    public function mapControllers(): void
    {
        $controllerMapper = new ControllerMapper($this->controllersPath);
        $this->routes = $controllerMapper->mapControllers();
    }

    public function useAuthorization(IMiddleware $authMiddleware = new AuthorizationMiddleware()): void
    {
        $this->addMiddleware($authMiddleware);
    }

    public function setViewsPath(string $path): void
    {
        $this->viewsPath = trim($path, '/');
    }

    public function setControllersPath(string $path): void
    {
        $this->controllersPath = trim($path, '/');
    }
}