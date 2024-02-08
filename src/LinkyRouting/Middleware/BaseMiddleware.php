<?php

namespace src\LinkyRouting\Middleware;

use src\LinkyRouting\Middleware\Interfaces\IMiddleware;
use src\LinkyRouting\Request;
use src\LinkyRouting\Responses\Response;

abstract class BaseMiddleware implements IMiddleware
{
    protected ?IMiddleware $next = null;

    function setNext(IMiddleware $next): void
    {
        $this->next = $next;
    }

    function invoke(Request $request): Response
    {
        if ($this->next === null) {
            return $request->execute();
        }

        return $this->next->invoke($request);
    }
}