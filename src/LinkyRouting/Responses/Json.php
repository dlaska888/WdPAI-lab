<?php

namespace src\LinkyRouting\Responses;

use JsonSerializable;
use src\LinkyRouting\enums\HttpStatusCode;

// Based on JSend https://github.com/omniti-labs/jsend
class Json extends Response implements JsonSerializable
{
    private mixed $data;
    private ?string $message;

    public function __construct(mixed $data = null, HttpStatusCode $code = HttpStatusCode::OK, string $message = null)
    {
        parent::__construct($code);
        $this->data = $data;
        $this->message = $message;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getStatus(): string
    {
        return match (true) {
            $this->getCode()->value >= 200 && $this->getCode()->value < 300 => 'success',
            $this->getCode()->value >= 400 && $this->getCode()->value < 500 => 'fail',
            default => 'error',
        };
    }

    public function jsonSerialize(): array
    {
        $resultArray['status'] = $this->getStatus();

        if ($this->message !== null) {
            $resultArray['message'] = $this->message;
        }

        if ($this->data !== null) {
            $resultArray['data'] = $this->data;
        }
        
        return $resultArray;
    }
}
