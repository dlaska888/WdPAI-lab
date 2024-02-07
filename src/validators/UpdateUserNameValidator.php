<?php

namespace src\Validators;

class UpdateUserNameValidator extends BaseValidator
{

    protected function addValidation(): void
    {
        $this
            ->notNull('userName')
            ->minLength('userName', 3)
            ->maxLength('userName', 50);
    }
}