<?php

namespace src\Hydrators\Attributes;

use Attribute;

#[Attribute]
class HydrationStrategy 
{
    public function __construct(public string $strategy)
    {
    }
}