<?php

namespace src\routing\middleware\interfaces;

use src\routing\Request;
use src\routing\responses\Response;

interface IMiddleware
{
    function setNext(IMiddleware $next) : void;
    function invoke(Request $request) : Response;
}