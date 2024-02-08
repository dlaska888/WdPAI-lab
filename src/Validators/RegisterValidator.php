<?php

namespace src\Validators;

class RegisterValidator extends BaseValidator
{
    public function addValidation(): void
    {
        $this
            ->notNull('userName', 'Please enter your username')
            ->minLength('userName', 3, 'Username must be at least 3 characters long')
            ->maxLength('userName', 50, 'Username cannot exceed 50 characters')
            ->notNull('email', 'Please enter your email')
            ->emailAddress('email', 'Invalid email format')
            ->notNull('password', 'Please enter password')
            ->minLength('password', 8, 'Password must be at least 8 characters long')
            ->maxLength('password', 255, 'Password cannot exceed 255 characters')
            ->notNull('passwordConfirm', 'Please enter password confirmation')
            ->equals('password', 'passwordConfirm', 'Passwords do not match');
    }
}
