<?php

namespace src\LinkyRouting\Responses;

use src\LinkyRouting\enums\HttpStatusCode;

class View extends Response
{
    private ?string $template;
    private array $variables;

    public function __construct(string $template = null, array $variables = [], HttpStatusCode $code = 
    HttpStatusCode::OK)
    {
        parent::__construct($code);
        $this->template = $template;
        $this->variables = $variables;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

}