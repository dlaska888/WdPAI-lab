<?php

require_once "src/enums/UserRole.php";

require_once "src/enums/UserRole.php";

class LinkyUser
{
    public ?int $user_id = null;
    public string $user_name;
    public string $email;
    public string $password_hash;
    public bool $email_confirmed = false;
    public UserRole $role = UserRole::NORMAL;
    public ?string $refresh_token = null;
    public ?DateTime $refresh_token_exp = null;

}
