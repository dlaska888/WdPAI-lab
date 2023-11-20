<?php

require_once "src/Database.php";

class Repo
{
    protected Database $database;

    public function __construct()
    {
        $this->database = new Database();
    }
}