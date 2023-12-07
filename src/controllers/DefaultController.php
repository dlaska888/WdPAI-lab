<?php

namespace src\Controllers;

use src\Attributes\ApiController;
use src\Attributes\httpMethod\HttpGet;
use src\Attributes\MvcController;
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
