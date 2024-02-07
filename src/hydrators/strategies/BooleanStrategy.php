<?php

namespace src\hydrators\strategies;

use InvalidArgumentException;
use src\hydrators\interfaces\IStrategy;

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
