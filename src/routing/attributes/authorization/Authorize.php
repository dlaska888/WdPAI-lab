<?php

namespace src\routing\attributes\authorization;

use Attribute;
use src\Enums\UserRole;

#[Attribute(Attribute::TARGET_CLASS)]
class Authorize
{
    public function __construct(public UserRole $role)
    {
    }
}