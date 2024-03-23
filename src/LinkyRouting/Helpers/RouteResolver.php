<?php

namespace LinkyApp\LinkyRouting\Helpers;

use LinkyApp\LinkyRouting\Route;

class RouteResolver
{

    public function matchHttpMethod(string $method, Route $route): bool
    {
        if ($method !== $route->getHttpMethod()) {
            return false;
        }

        return true;
    }

    public function matchUrlParts(string $url, Route $route): bool
    {
        $urlParts = explode("/", $url);
        $routeParts = explode("/", $route->getPath());

        if (count($urlParts) !== count($routeParts)) {
            return false;
        }

        foreach ($routeParts as $index => $part) {
            if ($part !== $urlParts[$index] && !str_starts_with($part, "{")) {
                return false;
            }
        }

        return true;
    }
}