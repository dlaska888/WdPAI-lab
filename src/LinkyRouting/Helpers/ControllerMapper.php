<?php

namespace src\LinkyRouting\helpers;

class ControllerMapper
{
    private string $controllersPath;
    private AttributeResolver $attributeResolver;

    public function __construct(string $controllersPath)
    {
        $this->controllersPath = $controllersPath;
        $this->attributeResolver = new AttributeResolver();
    }

    public function mapControllers(): array
    {
        // Get all PHP files in the controllers directory
        $phpFiles = glob($this->controllersPath . '/*.php');

        $routes = [];

        foreach ($phpFiles as $phpFile) {
            $className = $this->getClassName($phpFile);
            if (!class_exists($className)) {
                include $phpFile;
            }

            $routes = array_merge($this->attributeResolver->resolveControllerRoutes($className), $routes);
        }

        return $routes;
    }

    private function getClassName(string $phpFile): string
    {
        $filename = pathinfo($phpFile, PATHINFO_FILENAME);
        $namespace = str_replace('/', '\\', $this->controllersPath);
        return $namespace . '\\' . $filename;
    }
}
