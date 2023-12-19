<?php

namespace src\LinkyRouting\Responses;

use src\LinkyRouting\enums\HttpStatusCode;

class Json extends Response
{
    private mixed $data;

    public function __construct(mixed $data = null, HttpStatusCode $code = HttpStatusCode::OK)
    {
        parent::__construct($code);
        $this->data = $data;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

}
