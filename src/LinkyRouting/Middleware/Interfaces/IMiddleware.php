<?php

namespace src\LinkyRouting\middleware\interfaces;

use src\LinkyRouting\Request;
use src\LinkyRouting\Responses\Response;

interface IMiddleware
{
    function setNext(IMiddleware $next) : void;
    function invoke(Request $request) : Response;
}