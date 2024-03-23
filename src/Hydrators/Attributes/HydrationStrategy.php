<?php

namespace LinkyApp\Hydrators\Attributes;

use Attribute;

#[Attribute]
class HydrationStrategy 
{
    public function __construct(public string $strategy)
    {
    }
}