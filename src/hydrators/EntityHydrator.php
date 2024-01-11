<?php

namespace src\hydrators;

use InvalidArgumentException;
use ReflectionClass;
use src\Helpers\StringHelper;
use src\hydrators\attributes\HydrationStrategy;
use src\hydrators\interfaces\IHydrator;
use src\hydrators\interfaces\IStrategy;
use src\Models\Entities\Entity;


//Strategy design pattern
class EntityHydrator implements IHydrator
{
    // For optimisation purposes
    protected ?ReflectionClass $reflector = null;
    protected bool $cacheReflector;
    
    public function __construct($cacheReflector = false)
    {
        $this->cacheReflector = $cacheReflector;
    }

    public function hydrate(array $data, Entity $model): Entity
    {
        foreach ($data as $key => $value) {
            $fieldName = StringHelper::snakeToCamel($key);
            $strategy = $this->getHydrationStrategy($model, $fieldName);

            if($value === null && $this->checkIfNullable($fieldName)){
                $model->$fieldName = null;
                continue;
            }else if($value === null){
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
        if(!$this->cacheReflector){
            $this->reflector = new ReflectionClass($model);
        }else if ($this->reflector === null) {
            $this->reflector = new ReflectionClass($model);
        }

        if (!$this->reflector->hasProperty($fieldName)) {
            error_log("Field $fieldName not found in {${EntityHydrator::class}}");
            return null;
        }

        $hydrateAttribute = $this->reflector->getProperty($fieldName)->getAttributes(HydrationStrategy::class)[0] ?? null;

        if (!$hydrateAttribute) {
            return null;
        }

        $strategy = $hydrateAttribute->newInstance()->strategy;

        return new $strategy;
    }

    private function checkIfNullable(string $fieldName): bool
    {
        $property = $this->reflector->getProperty($fieldName);
        return $property->getType()->allowsNull(); 
    }
}
