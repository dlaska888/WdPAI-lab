<?php

namespace LinkyApp\LinkyRouting\Middleware\Interfaces;

use LinkyApp\LinkyRouting\Request;
use LinkyApp\LinkyRouting\Responses\Response;

interface IMiddleware
{
    function setNext(IMiddleware $next) : void;
    function invoke(Request $request) : Response;
}