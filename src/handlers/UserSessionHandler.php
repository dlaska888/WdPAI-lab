<?php

namespace src\Handlers;

class UserSessionHandler extends AppSessionHandler
{
    public function __construct()
    {
        parent::__construct();
    }


    public function getUserId(): ?string
    {
        return $this->isSessionSet() ? $_SESSION['user_id'] : null;
    }

    public function getUserName(): ?string
    {
        return $this->isSessionSet() ? $_SESSION['user_name'] : null;
    }

    public function getUserEmail(): ?string
    {
        return $this->isSessionSet() ? $_SESSION['user_email'] : null;
    }
    
    public function setSession($user): void
    {
        // Set session variables
        $_SESSION['user_id'] = $user->user_id;
        $_SESSION['user_name'] = $user->user_name;
        $_SESSION['user_email'] = $user->email;

        // Set session expiry time (e.g., 1 hour from now)
        $_SESSION['expiry_time'] = time() + 3600; // 1 hour
    }

    public function isSessionSet(): bool
    {
        return isset($_SESSION['user_id']) && $_SESSION['expiry_time'] > time();
    }

    public function unsetSession(): void
    {
        if ($this->isSessionSet()) {
            $_SESSION['expiry_time'] = time(); // session is invalid after 1 second
        }
    }

}