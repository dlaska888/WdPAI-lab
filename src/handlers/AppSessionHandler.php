<?php

namespace src\Handlers;

class AppSessionHandler
{
    public function __construct()
    {
        session_start();
    }

    public  function unsetSession() :void
    {
        // Unset all session variables
        session_unset();

        // Destroy the session
        session_destroy();
    }
}