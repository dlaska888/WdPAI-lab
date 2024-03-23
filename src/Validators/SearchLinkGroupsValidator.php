<?php

namespace LinkyApp\Validators;

class SearchLinkGroupsValidator extends BaseValidator
{
    protected function addValidation(): void
    {
        $this->notNull("name");        
    }
}