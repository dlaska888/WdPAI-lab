<?php

namespace src\Handlers;

use src\Models\Entities\LinkyUser;

class UserSessionHandler extends AppSessionHandler
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getUserId(): ?string
    {
        return $this->isSessionSet() ? $_SESSION['userId'] : null;
    }

    public function getUserEmail(): ?string
    {
        return $this->isSessionSet() ? $_SESSION['userEmail'] : null;
    }

    public function getUserRole(): ?string
    {
        return $this->isSessionSet() ? $_SESSION['userRole'] : null;
    }
    
    public function setSession(LinkyUser $user): void
    {
        // Set session variables
        $_SESSION['userId'] = $user->id;
        $_SESSION['userEmail'] = $user->email;
        $_SESSION['userRole'] = $user->role->value;

        // Set session expiry time (e.g., 1 hour from now)
        $_SESSION['expiryTime'] = time() + 3600; // 1 hour
    }

    public function isSessionSet(): bool
    {
        return isset($_SESSION['userId']) && $_SESSION['expiryTime'] > time();
    }

    public function unsetSession(): void
    {
        if ($this->isSessionSet()) {
            $_SESSION['expiryTime'] = time();
        }
    }

}