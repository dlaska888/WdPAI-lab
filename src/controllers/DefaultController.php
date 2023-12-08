<?php

namespace src\Controllers;

use src\attributes\controller\MvcController;
use src\Attributes\httpMethod\HttpGet;
use src\Attributes\Route;

#[MvcController]
class DefaultController extends AppController
{
    #[HttpGet]
    #[Route("index")]
    public function index(): void
    {
        $this->render("index");
    }
}
