<?php

namespace src\LinkyRouting\Responses;

use src\LinkyRouting\enums\HttpStatusCode;

class Error extends Response
{
    private mixed $data;
    private ?string $template;
    private string $controllerType;

    public function __construct(string $controllerType, mixed $data = [], string $template = null, HttpStatusCode $code =
    HttpStatusCode::INTERNAL_SERVER_ERROR)
    {
        parent::__construct($code);
        $this->controllerType = $controllerType;
        $this->data = $data;
        $this->template = $template;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function getControllerType(): string
    {
        return $this->controllerType;
    }
}