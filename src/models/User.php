<?php

require_once "src/enums/UserRole.php";

class User
{
    public int $userId;
    public string $userName;
    public string $email;
    public string $passwordHash;
    public bool $emailConfirmed;
    public UserRole $role;
    public ?string $refreshToken;
    public ?DateTime $refreshTokenExp;
}