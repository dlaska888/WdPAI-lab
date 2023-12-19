<?php

namespace src\Controllers;

use src\LinkyRouting\attributes\controller\Controller;
use src\LinkyRouting\attributes\httpMethod\HttpGet;
use src\LinkyRouting\attributes\Route;
use src\LinkyRouting\Responses\View;

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
