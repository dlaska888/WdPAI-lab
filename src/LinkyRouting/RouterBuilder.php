<?php

namespace src\LinkyRouting;

use src\LinkyRouting\exceptions\RouterBuilderException;
use src\LinkyRouting\helpers\ControllerMapper;
use src\LinkyRouting\helpers\HttpResponseHandler;
use src\LinkyRouting\helpers\RouteResolver;
use src\LinkyRouting\Interfaces\ISessionHandler;
use src\LinkyRouting\middleware\AuthorizationMiddleware;
use src\LinkyRouting\middleware\interfaces\IMiddleware;
use src\LinkyRouting\middleware\MiddlewareChain;

class RouterBuilder
{
    private array $routes = [];
    private MiddlewareChain $middlewareChain;
    private RouteResolver $routeResolver;
    private string $viewsPath;
    private string $controllersPath;

    public function __construct()
    {
        $this->middlewareChain = new MiddlewareChain();
    }

    /**
     * @throws RouterBuilderException
     */
    public function build(): Router
    {
        $router = new Router();

        if (empty($this->routeResolver)) {
            throw new RouterBuilderException("Session handler was not set, define session handler and pass 
            it by setSessionHandler() in router builder");
        }

        $router->setRouteResolver($this->routeResolver);
        $router->setRoutes($this->routes);
        $router->setMiddlewareChain($this->middlewareChain);
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

    public function useAuthorization(IMiddleware $authMiddleware = null): void
    {
        if($authMiddleware === null){
            $this->addMiddleware(new AuthorizationMiddleware($this->routeResolver));
            return;
        }
        
        $this->addMiddleware($authMiddleware);
    }

    public function setSessionHandler(ISessionHandler $sessionHandler): void
    {
        $this->routeResolver = new RouteResolver($sessionHandler);
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