<?php

namespace src\routing\responses;

use src\routing\enums\HttpStatusCode;

class ErrorView extends View
{
    private ?string $description;

    public function __construct(HttpStatusCode $code, $description = null)
    {
        parent::__construct('error', ['code' => $code, 'description' => $description], $code);
    }
}