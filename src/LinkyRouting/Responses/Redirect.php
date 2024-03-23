<?php

namespace LinkyApp\LinkyRouting\Responses;

use LinkyApp\LinkyRouting\Enums\HttpStatusCode;

class Redirect extends Response
{
    private string $url;

    public function __construct(string $url, HttpStatusCode $code = HttpStatusCode::MOVED_PERMANENTLY)
    {
        parent::__construct($code);
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}