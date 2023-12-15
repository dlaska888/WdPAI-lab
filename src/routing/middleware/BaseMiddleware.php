<?php

namespace src\routing\middleware;

use src\routing\middleware\interfaces\IMiddleware;
use src\routing\Request;
use src\routing\responses\Response;

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