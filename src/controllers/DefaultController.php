<?php

require_once 'src/controllers/AppController.php';

class DefaultController extends AppController
{
    public function index(): void
    {
        $this->render('index');
    }

    public function dashboard(): void
    {
        $this->render('dashboard');
    }

}
