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

    /**
     * @throws RouterBuilderException
     */
    public function useAuthorization(IMiddleware $authMiddleware = null): void
    {
        if ($authMiddleware === null) {
            if (empty($this->sessionHandler)) {
                throw new RouterBuilderException("Session handler was not set for default authorization, 
                define session handler and pass it by setSessionHandler() in RouterBuilder");
            }

            $this->addMiddleware(new AuthorizationMiddleware($this->sessionHandler));
            return;
        }

        $this->addMiddleware($authMiddleware);
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