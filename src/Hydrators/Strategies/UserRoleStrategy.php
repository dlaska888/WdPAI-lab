<?php

namespace LinkyApp\Hydrators\Strategies;

use LinkyApp\Enums\UserRole;
use LinkyApp\Hydrators\Interfaces\IStrategy;

class UserRoleStrategy implements IStrategy
{
    public function hydrate(mixed $value): UserRole
    {
        return UserRole::from($value);
    }

    public function extract(mixed $value): mixed
    {
        return $value->name;
    }
}