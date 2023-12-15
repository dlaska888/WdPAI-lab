<?php

namespace src\routing\responses;

use src\routing\enums\HttpStatusCode;

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