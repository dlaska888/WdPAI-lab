<?php

namespace src\LinkyRouting\Attributes\Authorization;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Authorize
{
    public function __construct(public array $roles)
    {
        $this->roles = array_map(fn($role) => strtoupper($role), $this->roles);
    }
}