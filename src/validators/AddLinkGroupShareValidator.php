<?php

namespace src\Validators;

class AddLinkGroupShareValidator extends BaseValidator
{
    public function addValidation(): void
    {
        $this
            ->notNull('email')
            ->emailAddress('email')
            ->notNull('permission')
            ->validatePermission('permission');
    }
}