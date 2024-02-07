<?php

namespace src\exceptions;

use Exception;
use src\Validators\ValidationResult;
use Throwable;

class ValidationException extends Exception
{
    private ValidationResult $validationResult;
    
    public function __construct(ValidationResult $validationResult, string $message = "", int $code = 0, ?Throwable 
    $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->validationResult = $validationResult;
    }

    public function getValidationResult(): ValidationResult
    {
        return $this->validationResult;
    }
}