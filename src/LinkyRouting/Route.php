<?php

namespace src\LinkyRouting;

class Route
{
    private string $path;
    private string $httpMethod;
    private string $controller;
    private string $action;
    private array $roles;

    public function __construct(string $path, string $httpMethod, string $controller, string $action, array $auth)
    {
        $this->path = $path;
        $this->httpMethod = $httpMethod;
        $this->controller = $controller;
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

    public function getAction(): string
    {
        return $this->action;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    // TODO decide whether keys are better for optimisation
    public function getKey(): string
    {
        return $this->httpMethod . "::" . $this->path;
    }

}
