<?php

namespace src\Hydrators\Strategies;

use src\Enums\UserRole;
use src\Hydrators\Interfaces\IStrategy;

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