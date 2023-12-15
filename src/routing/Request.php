<?php

namespace src\routing;

use src\routing\enums\HttpStatusCode;
use src\routing\responses\Response;

readonly class Request
{
    private Route $route;
    private array $params;
    
    public function __construct(Route $route, array $params)
    {
        $this->route = $route;
        $this->params = $params;
    }
    
    public function execute() : Response
    {
        $object = new ($this->route->getController());
        $action = $this->route->getAction();
        
        return $object->$action(...$this->params);
    }

    public function getRoute(): Route
    {
        return $this->route;
    }

    public function getParams(): array
    {
        return $this->params;
    }

}