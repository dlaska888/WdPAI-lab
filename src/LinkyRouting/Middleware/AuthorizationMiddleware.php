<?php

namespace src\LinkyRouting\middleware;

use src\LinkyRouting\enums\HttpStatusCode;
use src\LinkyRouting\helpers\RouteResolver;
use src\LinkyRouting\Interfaces\ISessionHandler;
use src\LinkyRouting\Request;
use src\LinkyRouting\Responses\ErrorView;
use src\LinkyRouting\Responses\Response;

class AuthorizationMiddleware extends BaseMiddleware
{
    private RouteResolver $routeResolver;

    public function __construct(RouteResolver $routeResolver)
    {
        $this->routeResolver = $routeResolver;
    }

    public function invoke(Request $request): Response
    {
        if (!$this->routeResolver->checkAuthorization($request->getRoute())) {
            return new ErrorView(HttpStatusCode::UNAUTHORIZED, "You are not authorized to access this resource");
        }

        return parent::invoke($request);
    }
}