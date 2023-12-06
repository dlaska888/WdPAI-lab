<?php

namespace src\Controllers;

use src\Attributes\ApiController;
use src\Attributes\Route;

#[ApiController]
class DefaultController extends AppController
{
    #[Route("")]
    public function index(): void
    {
        $this->render('index');
    }
}
