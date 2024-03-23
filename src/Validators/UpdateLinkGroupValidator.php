<?php

namespace LinkyApp\Validators;

class UpdateLinkGroupValidator extends BaseValidator
{
    public function addValidation(): void
    {
        $this
            ->minLength('name', 3)
            ->maxLength('name', 50);
    }
}