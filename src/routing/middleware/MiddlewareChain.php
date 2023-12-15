<?php

namespace src\routing\middleware;

use src\routing\middleware\interfaces\IMiddleware;

class MiddlewareChain extends BaseMiddleware
{
    private array $middlewares = [];

    public function add(IMiddleware $middleware) : void
    {
        if (empty($this->middlewares)){
            $this->setNext($middleware);            
        }else{
            end($this->middlewares)->setNext($middleware);
        }
        
        $this->middlewares[] = $middleware;
    }

}