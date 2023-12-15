<?php

namespace src\routing\helpers;

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

        $routes = array();

        foreach ($phpFiles as $phpFile) {
            $className = "src\\Controllers\\" . pathinfo($phpFile, PATHINFO_FILENAME);
            if (!class_exists($className)) {
                include $phpFile;
            }

            $routes = array_merge($this->attributeResolver->resolveControllerRoutes($className), $routes);
        }
        
        return $routes;
    }
    
}