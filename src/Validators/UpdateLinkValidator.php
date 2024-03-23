<?php

namespace LinkyApp\Validators;

class UpdateLinkValidator extends BaseValidator
{
    public function addValidation(): void
    {
        $this
            ->minLength('title', 3)
            ->maxLength('title', 50)
            ->url('url')
            ->minLength('url', 3)
            ->maxLength('url', 2000);
    }
}