<?php

namespace src\LinkyRouting\Interfaces;

interface ISessionHandler
{
    function getUserId(): mixed;
    function getUserRole(): ?string;
    public function isSessionSet(): bool;
}