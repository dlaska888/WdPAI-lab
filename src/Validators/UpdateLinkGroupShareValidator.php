<?php

namespace src\Validators;

class UpdateLinkGroupShareValidator extends BaseValidator
{
    public function addValidation(): void
    {
        $this
            ->notNull('permission')
            ->validatePermission('permission');
    }
}