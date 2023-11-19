<?php

require_once 'AppController.php';

class DefaultController extends AppController
{

    public function index(): void
    {
        $this->render('index');
    }

    public function login(): void
    {
        $this->render('login');
    }

    public function register(): void
    {
        $this->render('register');
    }

    public function dashboard(): void
    {
        $this->render('dashboard');
    }
}
