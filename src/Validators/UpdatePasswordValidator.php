<?php

namespace src\Validators;

class UpdatePasswordValidator extends BaseValidator
{

    protected function addValidation(): void
    {
        $this
            ->notNull('password')
            
            ->notNull('newPassword')
            ->minLength('newPassword', 8)
            ->maxLength('newPassword', 20)
            ->hasNumber('newPassword')
            ->hasLowerCase('newPassword')
            ->hasUpperCase('newPassword')
            ->hasSpecialCharacter('newPassword')
            
            ->notNull('newPasswordConfirm')
            ->equals('newPassword', 'newPasswordConfirm', 'Passwords do not match');
    }
}