<?php

namespace LinkyApp\LinkyRouting\Attributes\HttpMethod;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class HttpMethod
{
    public function __construct(public string $method)
    {
    }
}