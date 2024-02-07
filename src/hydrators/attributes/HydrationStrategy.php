<?php

namespace src\hydrators\attributes;

use Attribute;

#[Attribute]
class HydrationStrategy 
{
    public function __construct(public string $strategy)
    {
    }
}