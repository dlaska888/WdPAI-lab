<?php

namespace LinkyApp\LinkyRouting\Middleware;

use LinkyApp\LinkyRouting\Middleware\Interfaces\IMiddleware;

class MiddlewareChain extends BaseMiddleware
{
    private ?IMiddleware $last = null;

    public function add(IMiddleware $middleware): void
    {
        if ($this->last === null) {
            $this->setNext($middleware);
        } else {
            $this->last->setNext($middleware);
        }

        $this->last = $middleware;
    }
}