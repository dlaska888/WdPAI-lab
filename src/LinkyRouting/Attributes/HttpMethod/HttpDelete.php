<?php

namespace LinkyApp\LinkyRouting\Attributes\HttpMethod;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class HttpDelete extends HttpMethod
{
    public function __construct()
    {
        parent::__construct('DELETE');
    }
}