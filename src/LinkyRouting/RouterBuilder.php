<?php

namespace LinkyApp\LinkyRouting;

use LinkyApp\LinkyRouting\Exceptions\RouterBuilderException;
use LinkyApp\LinkyRouting\Helpers\ControllerMapper;
use LinkyApp\LinkyRouting\Helpers\HttpResponseHandler;
use LinkyApp\LinkyRouting\Interfaces\ISessionHandler;
use LinkyApp\LinkyRouting\Middleware\AuthorizationMiddleware;
use LinkyApp\LinkyRouting\Middleware\Interfaces\IMiddleware;
use LinkyApp\LinkyRouting\Middleware\MiddlewareChain;

class RouterBuilder
{
    private array $routes = [];
    private MiddlewareChain $middlewareChain;
    private ISessionHandler $sessionHandler;
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
        $router->setResponseHandler(new HttpResponseHandler($this->viewsPath));

        return $router;
    }

    public function addMiddleware(IMiddleware $middleware): void
    {
        $this->middlewareChain->add($middleware);
    }

    /**
     * @throws RouterBuilderException
     */
    public function mapControllers(): void
    {
        if(empty($this->controllersPath)){
            throw new RouterBuilderException("Controllers path was not set");
        }
        
        $controllerMapper = new ControllerMapper($this->controllersPath);
        $this->routes = $controllerMapper->mapControllers();
    }

    /**
     * @throws RouterBuilderException
     */
    public function useAuthorization(IMiddleware $authMiddleware = null): void
    {
        if ($authMiddleware === null && empty($this->sessionHandler)) {
            throw new RouterBuilderException("Session handler was not set for default authorization");
        }

        $this->addMiddleware($authMiddleware ?? new AuthorizationMiddleware($this->sessionHandler));
    }

    public function setSessionHandler(ISessionHandler $sessionHandler): void
    {
        $this->sessionHandler = $sessionHandler;
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