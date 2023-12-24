<?php

namespace src\Controllers;

use src\LinkyRouting\enums\HttpStatusCode;
use src\Validators\ValidationResult;

abstract class AppController
{
    private array|null $requestBody;

    public function __construct()
    {
        $this->requestBody = json_decode(file_get_contents('php://input'), true);
    }

    protected function getRequestBody(): array|null
    {
        return $this->requestBody;
    }

    protected function getValidationResult(?array $data, string $validatorClass): ValidationResult
    {
        if ($data === null) {
            return new ValidationResult(false, ['Invalid request data']);
        }

        return (new $validatorClass($data))->validate();
    }
}
