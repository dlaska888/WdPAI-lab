<?php

namespace src\Hydrators\Strategies;

use src\Enums\GroupPermissionLevel;
use src\Hydrators\Interfaces\IStrategy;

class GroupPermissionLevelStrategy implements IStrategy
{
    public function hydrate(mixed $value): GroupPermissionLevel
    {
        return GroupPermissionLevel::from($value);
    }

    public function extract(mixed $value): mixed
    {
        return $value->name;
    }
}