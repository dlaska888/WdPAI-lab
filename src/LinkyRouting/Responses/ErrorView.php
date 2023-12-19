<?php

namespace src\LinkyRouting\Responses;

use src\LinkyRouting\enums\HttpStatusCode;

class ErrorView extends View
{
    private ?string $description;

    public function __construct(HttpStatusCode $code, $description = null)
    {
        parent::__construct('error', ['code' => $code, 'description' => $description], $code);
    }
}