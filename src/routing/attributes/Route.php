<?php

namespace src\routing\attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(public string $route)
    {
    }
}