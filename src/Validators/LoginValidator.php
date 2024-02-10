<?php

namespace src\Validators;

class LoginValidator extends BaseValidator
{
    public function addValidation(): void
    {
        $this
            ->notNull('email', 'Please enter your email or username')
            ->notNull('password', 'Please enter your password');
    }
}
