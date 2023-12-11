<?php

namespace src\Validators;

class RegisterValidator extends BaseValidator
{
    public function addValidation(): void
    {
        $this
            ->notNull('userName')
            ->minLength('userName', 3)
            ->maxLength('userName', 50)
            ->notNull('email')
            ->emailAddress('email')
            ->notNull('password')
            ->minLength('password', 6)
            ->maxLength('password', 255)
            ->notNull('passwordConfirm')
            ->equals('password', 'passwordConfirm', 'Passwords do not match');
    }
}
