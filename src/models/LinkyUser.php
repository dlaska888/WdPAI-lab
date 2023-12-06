<?php

namespace src\Models;

use src\Enums\UserRole;
use src\Helpers\UUIDGenerator;
use DateTime;

class LinkyUser
{
    public string $user_id;
    public string $user_name;
    public string $email;
    public string $password_hash;
    public bool $email_confirmed;
    public UserRole $role;
    public ?string $refresh_token;
    public ?DateTime $refresh_token_exp;

    public function __construct(
        string   $user_name,
        string   $email,
        string   $password_hash,
        string   $user_id = null,
        bool     $email_confirmed = false,
        UserRole $role = UserRole::NORMAL,
        string   $refresh_token = null,
        DateTime $refresh_token_exp = null
    )
    {
        $this->user_id = $user_id ?? UUIDGenerator::genUUID();
        $this->user_name = $user_name;
        $this->email = $email;
        $this->password_hash = $password_hash;
        $this->email_confirmed = $email_confirmed;
        $this->role = $role ?: UserRole::NORMAL;
        $this->refresh_token = $refresh_token;
        $this->refresh_token_exp = $refresh_token_exp;
    }

}

