<?php

namespace src\Controllers;

use src\exceptions\ValidationException;
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

    protected function validateRequestData(?array $data, string $validatorClass): void
    {
        if ($data === null) {
            throw new ValidationException(new ValidationResult(false, ["Empty request body"]));
        }
        
        $validationResult = (new $validatorClass($data))->validate();
        if(!$validationResult->isSuccess())
            throw new ValidationException($validationResult);
    }
}
