<?php

namespace LinkyApp\Hydrators\Strategies;

use LinkyApp\Enums\GroupPermissionLevel;
use LinkyApp\Hydrators\Interfaces\IStrategy;

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