<?php

namespace src\LinkyRouting\helpers;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use src\LinkyRouting\attributes\authorization\Authorize;
use src\LinkyRouting\attributes\authorization\SkipAuthorization;
use src\LinkyRouting\attributes\controller\Controller;
use src\LinkyRouting\attributes\httpMethod\HttpMethod;
use src\LinkyRouting\Route;

class AttributeResolver
{
    public function resolveControllerRoutes(string $controllerName): array
    {
        $controllerReflection = $this->getReflectionClass($controllerName);
        $routes = array();
        
        if(!$this->isController($controllerName))
            return [];

        foreach ($controllerReflection->getMethods() as $method) {
            $methodName = $method->getName();
            $routeAttributes = $method->getAttributes(\src\LinkyRouting\attributes\Route::class);

            if (empty($routeAttributes)) {
                continue;
            }

            $path = $routeAttributes[0]->getArguments()[0] ?? $methodName;
            $httpMethods = $this->resolveHttpMethods($controllerName, $methodName);
            $auth = $this->resolveAuthorization($controllerName, $methodName);

            foreach ($httpMethods as $httpMethod) {
                $route = new Route($path, $httpMethod, $controllerName, $methodName, $auth);
                $routes[] = $route;
            }
        }

        return $routes;
    }

    public function resolveHttpMethods(string $controllerName, string $methodName): array
    {
        $reflection = $this->getReflectionMethod($controllerName, $methodName);
        if ($reflection === null)
            return [];

        $httpMethodAttributes = array_filter($reflection->getAttributes(),
            fn($attribute) => is_subclass_of($attribute->getName(), HttpMethod::class));

        $httpMethods = array();

        foreach ($httpMethodAttributes as $httpMethodAttribute) {
            $httpMethodAttribute = $httpMethodAttribute->newInstance();
            $httpMethods[] = $httpMethodAttribute->method;
        }

        if (empty($httpMethodAttributes)) {
            $httpMethods[] = 'GET';
        }

        return $httpMethods;
    }

    public function resolveAuthorization(string $controllerName, string $methodName): array
    {
        $reflection = $this->getReflectionMethod($controllerName, $methodName);
        if ($reflection === null)
            return [];

        $attributes = $reflection->getAttributes(SkipAuthorization::class);

        if (!empty($attributes))
            return [];

        $classAttributes = $reflection->getDeclaringClass()->getAttributes(Authorize::class);

        if (empty($classAttributes))
            return [];

        return $classAttributes[0]->newInstance()->roles;
    }

    private function isController(string $className): bool
    {
        $reflection = $this->getReflectionClass($className);
        if ($reflection === null)
            return false;

        if (empty($reflection->getAttributes(Controller::class))) {
            return false;
        }

        return true;
    }

    private function getReflectionClass(string $className): ?ReflectionClass
    {
        try {
            $reflection = new ReflectionClass($className);
        } catch (ReflectionException) {
            return null;
        }

        return $reflection;
    }

    private function getReflectionMethod(string $className, string $methodName): ?ReflectionMethod
    {
        try {
            $reflection = new ReflectionMethod($className, $methodName);
        } catch (ReflectionException) {
            return null;
        }

        return $reflection;
    }

}