<?php

namespace src\hydrators\strategies;

use src\Enums\UserRole;
use src\hydrators\interfaces\IStrategy;

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