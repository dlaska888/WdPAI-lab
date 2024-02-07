<?php

namespace src\Validators;

class AddLinkValidator extends BaseValidator
{
    public function addValidation(): void
    {
        $this
            ->notNull('title')
            ->minLength('title', 3)
            ->maxLength('title', 50)
            ->notNull('url')
            ->url('url')
            ->minLength('url', 3)
            ->maxLength('url', 2000);

    }
}