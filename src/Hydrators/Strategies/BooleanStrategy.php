<?php

namespace src\Hydrators\Strategies;

use InvalidArgumentException;
use src\Hydrators\Interfaces\IStrategy;

class BooleanStrategy implements IStrategy
{
    public function hydrate(mixed $value): bool
    {
        return (bool) $value;
    }

    public function extract($value): int
    {
        if (!is_bool($value) && !is_numeric($value)) {
            throw new InvalidArgumentException('Invalid value provided for boolean extraction');
        }

        return (int) $value;
    }
}
