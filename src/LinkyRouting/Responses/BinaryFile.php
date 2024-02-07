<?php

namespace src\LinkyRouting\Responses;

use src\LinkyRouting\enums\HttpStatusCode;

class BinaryFile extends Response
{
    protected string $filePath;

    public function __construct(string $filePath, HttpStatusCode $code = HttpStatusCode::OK)
    {
        parent::__construct($code);
        $this->filePath = $filePath;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
