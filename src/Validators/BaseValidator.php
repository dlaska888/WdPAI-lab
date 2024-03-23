<?php

namespace LinkyApp\Validators;

use LinkyApp\Enums\GroupPermissionLevel;

abstract class BaseValidator
{
    protected array $data;
    protected array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function validate(): ValidationResult
    {
        $this->addValidation();

        if (!empty($this->errors)) {
            return new ValidationResult(false, $this->errors);
        }

        return new ValidationResult(true, []);
    }

    protected abstract function addValidation(): void;

    protected function addError(string $field, string $message): BaseValidator
    {
        $this->errors[$field] = $message;
        return $this;
    }

    protected function hasValue(string $field): bool
    {
        return isset($this->data[$field]) && $this->data[$field] !== null;
    }

    public function notNull(string $field, string $message = 'Value cannot be null.'): BaseValidator
    {
        if (!$this->hasValue($field)) {
            $this->addError($field, $message);
        }

        return $this;
    }

    public function minLength(string $field, string $minLength, string $message = 'Value is too short.'): BaseValidator
    {
        if ($this->hasValue($field) && strlen($this->data[$field]) < $minLength) {
            $this->addError($field, $message);
        }

        return $this;
    }

    public function maxLength(string $field, string $maxLength, string $message = 'Value is too long.'): BaseValidator
    {
        if ($this->hasValue($field) && strlen($this->data[$field]) > $maxLength) {
            $this->addError($field, $message);
        }

        return $this;
    }

    public function minValue(string $field, int $minValue, string $message = 'Value is too small.'): BaseValidator
    {
        if ($this->hasValue($field) && $this->data[$field] < $minValue) {
            $this->addError($field, $message);
        }

        return $this;
    }

    public function maxValue(string $field, int $maxValue, string $message = 'Value is too large.'): BaseValidator
    {
        if ($this->hasValue($field) && $this->data[$field] > $maxValue) {
            $this->addError($field, $message);
        }

        return $this;
    }

    public function equals(string $field, string $otherField, string $message = 'Values are not equal.'): BaseValidator
    {
        if (($this->hasValue($field) && $this->hasValue($otherField)) &&
            $this->data[$field] !== $this->data[$otherField]) {
            $this->addError($field, $message);
        }

        return $this;
    }

    public function in_array(string $field, array $array, string $message = 'Value is not in array.'):
    BaseValidator
    {
        if (($this->hasValue($field)) &&
            !in_array($this->data[$field], $array)) {
            $this->addError($field, $message);
        }

        return $this;
    }

    public function emailAddress(string $field, string $message = 'Invalid email address format.'): BaseValidator
    {
        if ($this->hasValue($field) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $message);
        }

        return $this;
    }

    public function url(string $field, string $message = 'Invalid URL format.'): BaseValidator
    {
        if ($this->hasValue($field) && !filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
            $this->addError($field, $message);
        }

        return $this;
    }

    public function validatePermission(string $field, string $message = 'Invalid permission level.'): BaseValidator
    {
        // Assume GroupPermissionLevel is an enum-like class
        if ($this->hasValue($field) && !GroupPermissionLevel::tryFrom($this->data[$field])) {
            $this->addError($field, $message);
        }

        return $this;
    }

    public function regex(string $field, string $pattern, string $message = 'Value does not match the required pattern.'): BaseValidator
    {
        if ($this->hasValue($field) && !preg_match($pattern, $this->data[$field])) {
            $this->addError($field, $message);
        }

        return $this;
    }

    public function hasUpperCase(string $field, int $count = 1, string $message = 'Password must contain at least %d uppercase letter(s).'): BaseValidator
    {
        if ($this->hasValue($field) && preg_match_all('/[A-Z]/', $this->data[$field]) < $count) {
            $this->addError($field, sprintf($message, $count));
        }

        return $this;
    }

    public function hasLowerCase(string $field, int $count = 1, string $message = 'Password must contain at least %d lowercase letter(s).'): BaseValidator
    {
        if ($this->hasValue($field) && preg_match_all('/[a-z]/', $this->data[$field]) < $count) {
            $this->addError($field, sprintf($message, $count));
        }

        return $this;
    }

    public function hasNumber(string $field, int $count = 1, string $message = 'Password must contain at least %d number(s).'): BaseValidator
    {
        if ($this->hasValue($field) && preg_match_all('/[0-9]/', $this->data[$field]) < $count) {
            $this->addError($field, sprintf($message, $count));
        }

        return $this;
    }

    public function hasSpecialCharacter(string $field, int $count = 1, string $message = 'Password must contain at least %d special character(s).'): BaseValidator
    {
        if ($this->hasValue($field) && preg_match_all('/[^A-Za-z0-9]/', $this->data[$field]) < $count) {
            $this->addError($field, sprintf($message, $count));
        }

        return $this;
    }
}
