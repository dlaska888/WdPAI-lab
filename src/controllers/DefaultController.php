<?php

namespace src\Controllers;

use src\routing\attributes\controller\Controller;
use src\routing\attributes\httpMethod\HttpGet;
use src\routing\attributes\Route;
use src\routing\responses\View;

#[Controller]
class DefaultController extends AppController
{
    #[HttpGet]
    #[Route("index")]
    public function index(): View
    {
        return new View('index');
    }
}
