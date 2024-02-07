<?php

namespace src\hydrators\strategies;

use src\Enums\GroupPermissionLevel;
use src\hydrators\interfaces\IStrategy;

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