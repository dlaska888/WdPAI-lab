<?php

namespace src\Handlers;

class AppSessionHandler
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function unsetSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            return;
        }
        
        // Unset all session variables
        session_unset();

        // Destroy the session
        session_destroy();
    }
}