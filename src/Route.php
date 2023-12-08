<?php

namespace src;

use src\Enums\UserRole;

class Route
{
    private string $path;
    private string $httpMethod;
    private string $controller;
    private string $action;
    private ?UserRole $auth;

    public function __construct(string $path, string $httpMethod, string $controller, string $action, ?UserRole $auth)
    {
        $this->path = $path;
        $this->httpMethod = $httpMethod;
        $this->controller = $controller;
        $this->action = $action;
        $this->auth = $auth;
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

    public function getAuth(): ?UserRole
    {
        return $this->auth;
    }
    
    public function getKey() : string
    {
        return $this->httpMethod . "::" . $this->path;
    }
}
