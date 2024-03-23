<?php

namespace LinkyApp\Validators;

class GetWebTitle extends BaseValidator
{
    protected function addValidation(): void
    {
        $this
            ->notNull('url')
            ->url('url');
    }
}