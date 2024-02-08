<?php

namespace src\LinkyRouting\Responses;

use src\LinkyRouting\Enums\HttpStatusCode;
use src\LinkyRouting\Request;

class Error extends Response
{
    private string $message;
    private mixed $data;
    private ?string $template;
    private ?Request $request;

    public function __construct(?Request $request, string $message, HttpStatusCode $code = 
    HttpStatusCode::INTERNAL_SERVER_ERROR, mixed $data = null, string $template = "error")
    {
        parent::__construct($code);
        $this->request = $request;
        $this->message = $message;
        $this->data = $data;
        $this->template = $template;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }
}