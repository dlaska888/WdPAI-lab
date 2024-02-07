<?php

namespace src\Validators;

class SearchLinkGroupsValidator extends BaseValidator
{
    protected function addValidation(): void
    {
        $this->notNull("name");        
    }
}