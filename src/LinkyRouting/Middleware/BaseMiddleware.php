<?php

namespace LinkyApp\LinkyRouting\Middleware;

use LinkyApp\LinkyRouting\Middleware\Interfaces\IMiddleware;
use LinkyApp\LinkyRouting\Request;
use LinkyApp\LinkyRouting\Responses\Response;

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