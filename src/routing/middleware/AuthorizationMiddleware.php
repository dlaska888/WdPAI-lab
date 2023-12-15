<?php

namespace src\routing\middleware;

use src\routing\enums\HttpStatusCode;
use src\routing\helpers\RouteResolver;
use src\routing\Request;
use src\routing\responses\ErrorView;
use src\routing\responses\Response;

class AuthorizationMiddleware extends BaseMiddleware
{
    private RouteResolver $routeResolver;

    public function __construct()
    {
        $this->routeResolver = new RouteResolver();
    }

    public function invoke(Request $request): Response
    {
        if (!$this->routeResolver->checkAuthorization($request->getRoute())) {
            return new ErrorView(HttpStatusCode::UNAUTHORIZED, "You are not authorized to access this resource");
        }

        return parent::invoke($request);
    }
}