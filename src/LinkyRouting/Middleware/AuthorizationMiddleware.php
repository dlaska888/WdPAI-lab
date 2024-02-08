<?php

namespace src\LinkyRouting\Middleware;

use src\LinkyRouting\Enums\HttpStatusCode;
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
            return new Error(
                $request, 
                "You are not authorized to access this resource", 
                HttpStatusCode::UNAUTHORIZED
            );
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
        return !(empty($userRole) || !in_array($userRole, $roles));
    }
}