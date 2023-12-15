<?php

namespace src\routing\helpers;

use src\Handlers\UserSessionHandler;
use src\Repos\UserRepo;
use src\routing\Route;

class RouteResolver
{
    private UserSessionHandler $sessionHandler;
    private UserRepo $userRepo;

    public function __construct()
    {
        $this->sessionHandler = new UserSessionHandler();
        $this->userRepo = new UserRepo();
    }

    public function matchHttpMethod(Route $route): bool
    {
        if ($_SERVER['REQUEST_METHOD'] !== $route->getHttpMethod()) {
            return false;
        }

        return true;
    }

    public function matchUrlParts(string $url, Route $route): bool
    {
        $urlParts = explode("/", $url);
        $routeParts = explode("/", $route->getPath());
        
        if(count($urlParts) !== count($routeParts))
            return false;

        foreach ($routeParts as $index => $part) {
            if ($part !== $urlParts[$index] && !str_starts_with($part, '{')) {
                return false;
            }
        }

        return true;
    }

    public function checkAuthorization(Route $route): bool
    {
        $requiredUserRole = $route->getAuth();

        // No authentication needed
        if (!$requiredUserRole) {
            return true;
        }

        // No session 
        $userId = $this->sessionHandler->getUserId();
        if (!$userId)
            return false;

        // Invalid user
        $currentUser = $this->userRepo->findById($this->sessionHandler->getUserId());
        if (!$currentUser) {
            return false;
        }

        return $currentUser->role === $requiredUserRole;
    }
}