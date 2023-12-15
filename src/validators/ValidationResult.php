<?php

namespace src\Validators;

use JsonSerializable;

class ValidationResult implements JsonSerializable
{
    private bool $success;
    private array $errors;

    public function __construct(bool $success, array $errors = [])
    {
        $this->success = $success;
        $this->errors = $errors;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function jsonSerialize(): array
    {
        return [
            'success' => $this->success,
            'errors' => $this->errors,
        ];
    }
}
