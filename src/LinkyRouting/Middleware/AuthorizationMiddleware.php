<?php

namespace LinkyApp\LinkyRouting\Middleware;

use LinkyApp\LinkyRouting\Enums\HttpStatusCode;
use LinkyApp\LinkyRouting\Interfaces\ISessionHandler;
use LinkyApp\LinkyRouting\Request;
use LinkyApp\LinkyRouting\Responses\Error;
use LinkyApp\LinkyRouting\Responses\Response;

class AuthorizationMiddleware extends BaseMiddleware
{
    private ISessionHandler $sessionHandler;

    public function __construct(ISessionHandler $sessionHandler)
    {
        $this->sessionHandler = $sessionHandler;
    }

    public function invoke(Request $request): Response
    {
        $roles = $request->getRoute()->getRoles();
        $userRole = $this->sessionHandler->getUserRole();
        
        //No authentication needed
        if(empty($roles)){
            return parent::invoke($request);
        }
        
        //Authentication fail
        if(empty($userRole)){
            return new Error(
                $request,
                "You need to be logged in",
                HttpStatusCode::UNAUTHORIZED
            );
        }
            
        //Authorization by user role fail
        if(!in_array($userRole, $roles)){
            return new Error(
                $request,
                "You are not authorized to access this resource",
                HttpStatusCode::FORBIDDEN
            );
        }
        
        return parent::invoke($request);
    }
}