<?php

namespace src\Hydrators;

use InvalidArgumentException;
use ReflectionClass;
use src\Helpers\StringHelper;
use src\Hydrators\Attributes\HydrationStrategy;
use src\Hydrators\Attributes\SkipHydration;
use src\Hydrators\Interfaces\IHydrator;
use src\Hydrators\Interfaces\IStrategy;
use src\Models\Entities\Entity;


//Strategy design pattern
class EntityHydrator implements IHydrator
{
    // For optimisation purposes
    protected ?ReflectionClass $reflector = null;
    protected bool $cacheReflector;
    
    public function __construct($cacheReflector = true)
    {
        $this->cacheReflector = $cacheReflector;
    }

    public function hydrate(array $data, Entity $model): Entity
    {
        foreach ($data as $key => $value) {
            $fieldName = StringHelper::snakeToCamel($key);
            
            if($this->shouldSkipHydration($model, $fieldName)){
                continue;
            }
            
            $strategy = $this->getHydrationStrategy($model, $fieldName);

            if($value === null && $this->checkIfNullable($fieldName)){
                $model->$fieldName = null;
                continue;
            }
            
            if($value === null){
                throw new InvalidArgumentException("$fieldName is not nullable, null provided.");
            }

            if ($strategy) {
                $value = $strategy->hydrate($value);
            }

            $model->$fieldName = $value;
        }

        return $model;
    }

    public function extract(Entity $model): array
    {
        $fields = get_object_vars($model);
        $data = [];

        foreach ($fields as $key => $value) {
            if($this->shouldSkipHydration($model, $key)){
                continue;
            }
            
            $strategy = $this->getHydrationStrategy($model, $key);

            if ($strategy) {
                $value = $strategy->extract($value);
            }

            $key = StringHelper::camelToSnake($key);

            $data[$key] = $value;
        }

        return $data;
    }

    private function getHydrationStrategy(Entity $model, string $fieldName): ?IStrategy
    {
        $reflector = $this->getReflector($model);

        if (!$reflector->hasProperty($fieldName)) {
            error_log("Field $fieldName not found in {${EntityHydrator::class}}");
            return null;
        }

        $hydrateAttribute = $reflector->getProperty($fieldName)->getAttributes(HydrationStrategy::class)[0] ?? null;

        if (!$hydrateAttribute) {
            return null;
        }

        $strategy = $hydrateAttribute->newInstance()->strategy;

        return new $strategy;
    }

    private function shouldSkipHydration(Entity $model, string $fieldName): bool
    {
        $reflector = $this->getReflector($model);

        if (!$reflector->hasProperty($fieldName)) {
            error_log("Field $fieldName not found in {${EntityHydrator::class}}");
            return false;
        }

        $skipHydrationAttribute = $reflector->getProperty($fieldName)->getAttributes(SkipHydration::class)[0] ?? null;

        return $skipHydrationAttribute !== null;
    }
    
    private function getReflector(Entity $model) : ReflectionClass
    {
        if (!$this->cacheReflector) {
            return new ReflectionClass($model);
        } 
        
        if ($this->reflector === null) {
            $this->reflector = new ReflectionClass($model);
        }
        
        return $this->reflector;
    }

    private function checkIfNullable(string $fieldName): bool
    {
        $property = $this->reflector->getProperty($fieldName);
        return $property->getType()->allowsNull(); 
    }
}
