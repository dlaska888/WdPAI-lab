<?php

namespace LinkyApp\Validators;

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

            ->notNull('password')
            ->minLength('password', 8)
            ->maxLength('password', 20)
            ->hasNumber('password')
            ->hasLowerCase('password')
            ->hasUpperCase('password')
            ->hasSpecialCharacter('password')
            
            ->notNull('passwordConfirm', 'Please enter password confirmation')
            ->equals('password', 'passwordConfirm', 'Passwords do not match');
    }
}
