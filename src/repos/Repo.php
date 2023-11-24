<?php

require_once "src/Database.php";

class Repo
{
    protected Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }
}