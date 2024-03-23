<?php

namespace LinkyApp\LinkyRouting\Attributes\HttpMethod;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class HttpPut extends HttpMethod
{
    public function __construct()
    {
        parent::__construct('PUT');
    }
}