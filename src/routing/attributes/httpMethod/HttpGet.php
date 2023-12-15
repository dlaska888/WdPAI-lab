<?php

namespace src\routing\attributes\httpMethod;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class HttpGet extends HttpMethod
{
    public function __construct()
    {
        parent::__construct('GET');
    }
}