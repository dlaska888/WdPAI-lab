<?php

namespace src\LinkyRouting\middleware;

use src\LinkyRouting\middleware\interfaces\IMiddleware;

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