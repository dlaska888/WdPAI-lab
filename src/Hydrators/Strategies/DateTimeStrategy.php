<?php

namespace src\Hydrators\Strategies;

use DateTime;
use InvalidArgumentException;
use src\Hydrators\Interfaces\IStrategy;

class DateTimeStrategy implements IStrategy
{
    public function hydrate(mixed $value): DateTime
    {
        if (!is_string($value) || strtotime($value) === false) {
            throw new InvalidArgumentException("Invalid date string provided for hydration $value");
        }

        return new DateTime($value);
    }

    public function extract(mixed $value): string
    {
        if (!$value instanceof DateTime) {
            throw new InvalidArgumentException("Invalid DateTime object provided for extraction $value");
        }

        return $value->format('Y-m-d H:i:s');
    }
}
