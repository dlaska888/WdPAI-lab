<?php

namespace LinkyApp\LinkyRouting;

readonly class Route
{
    private string $path;
    private string $httpMethod;
    private string $controller;
    private string $action;
    private array $roles;
    private string $controllerType;

    public function __construct(string $path, string $httpMethod, string $controller, string $controllerType, string $action, array $auth)
    {
        $this->path = $path;
        $this->httpMethod = $httpMethod;
        $this->controller = $controller;
        $this->controllerType = $controllerType;
        $this->action = $action;
        $this->roles = $auth;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getControllerType(): string
    {
        return $this->controllerType;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

}
