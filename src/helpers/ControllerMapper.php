<?php

namespace src\Helpers;

class ControllerMapper
{
    private AttributeResolver $attributeResolver;

    public function __construct()
    {
        $this->attributeResolver = new AttributeResolver();
    }

    public function mapControllers(): array
    {
        // Specify the directory where your controllers are located
        $controllersDirectory = 'src/controllers';

        // Get all PHP files in the controllers directory
        $phpFiles = glob($controllersDirectory . '/*.php');

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