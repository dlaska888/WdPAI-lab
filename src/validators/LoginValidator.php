<?php

namespace src\Validators;

class LoginValidator extends BaseValidator
{
    public function addValidation(): void
    {
        $this
            ->notNull('email')
            ->emailAddress('email')
            ->notNull('password');
    }
}
