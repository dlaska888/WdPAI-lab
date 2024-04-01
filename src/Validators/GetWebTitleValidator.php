<?php

namespace LinkyApp\Validators;

class GetWebTitleValidator extends BaseValidator
{
    protected function addValidation(): void
    {
        $this
            ->notNull('url')
            ->url('url')
            ->minValue('maxLength', 0);

    }
}