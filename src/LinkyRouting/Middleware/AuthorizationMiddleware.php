<?php

namespace src\LinkyRouting\middleware;

use src\LinkyRouting\enums\HttpStatusCode;
use src\LinkyRouting\helpers\RouteResolver;
use src\LinkyRouting\Interfaces\ISessionHandler;
use src\LinkyRouting\Request;
use src\LinkyRouting\Responses\ErrorView;
use src\LinkyRouting\Responses\Response;
use src\LinkyRouting\Route;

class AuthorizationMiddleware extends BaseMiddleware
{
    private ISessionHandler $sessionHandler;

    public function __construct(ISessionHandler $sessionHandler)
    {
        $this->sessionHandler = $sessionHandler;
    }

    public function invoke(Request $request): Response
    {
        if (!$this->checkAuthorization($request->getRoute())) {
            return new ErrorView(HttpStatusCode::UNAUTHORIZED, "You are not authorized to access this resource");
        }

        return parent::invoke($request);
    }

    private function checkAuthorization(Route $route): bool
    {
        $roles = $route->getRoles();

        // No authentication needed
        if (empty($roles)) {
            return true;
        }

        // No session 
        $userRole = $this->sessionHandler->getUserRole();
        if (empty($userRole) || !in_array($userRole, $roles))
            return false;

        return true;
    }
}