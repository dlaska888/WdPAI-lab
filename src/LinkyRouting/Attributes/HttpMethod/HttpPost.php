<?php

namespace src\LinkyRouting\Attributes\HttpMethod;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class HttpPost extends HttpMethod
{
    public function __construct()
    {
        parent::__construct('POST');
    }
}