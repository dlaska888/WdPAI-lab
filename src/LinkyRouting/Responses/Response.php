<?php

namespace LinkyApp\LinkyRouting\Responses;

use LinkyApp\LinkyRouting\Enums\HttpStatusCode;

abstract class Response
{
    protected HttpStatusCode $code;

    public function __construct(HttpStatusCode $code)
    {
        $this->code = $code;
    }

    public function getCode(): HttpStatusCode
    {
        return $this->code;
    }

}