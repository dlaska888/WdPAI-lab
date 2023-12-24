<?php

namespace src\LinkyRouting\middleware;

use src\LinkyRouting\enums\HttpStatusCode;
use src\LinkyRouting\Interfaces\ISessionHandler;
use src\LinkyRouting\Request;
use src\LinkyRouting\Responses\Error;
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
            return new Error($request->getRoute()->getControllerType(), "You are not authorized to access this resource", 
                'error', HttpStatusCode::UNAUTHORIZED);
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