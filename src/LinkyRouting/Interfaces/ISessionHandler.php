<?php

namespace LinkyApp\LinkyRouting\Interfaces;

interface ISessionHandler
{
    function getUserId(): mixed;
    function getUserRole(): ?string;
    public function isSessionSet(): bool;
}