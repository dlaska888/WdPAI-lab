<?php

namespace src\Validators;

class AddLinkGroupValidator extends BaseValidator
{
    public function addValidation(): void
    {
        $this
            ->notNull('name')
            ->minLength('name', 3)
            ->maxLength('name', 50);
    }
}