<?php

namespace src\Attributes\httpMethod;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class HttpMethod
{
    public function __construct(public string $method)
    {
    }
}