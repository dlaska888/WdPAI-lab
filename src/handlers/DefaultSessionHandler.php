<?php

class DefaultSessionHandler extends AppSessionHandler
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setSession(array $args): void
    {
        foreach ($args as $key => $value){
            $_SESSION[$key] = $value;
        }
    }

    public function isSessionSet(array $args): bool
    {
        foreach ($args as $key) {
            if (!isset($_SESSION[$key])) {
                return false;
            }
        }
        return true;
    }

}