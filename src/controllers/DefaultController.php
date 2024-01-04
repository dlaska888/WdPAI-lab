<?php

namespace src\Controllers;

use src\exceptions\NotFoundException;
use src\LinkyRouting\attributes\controller\MvcController;
use src\LinkyRouting\attributes\httpMethod\HttpGet;
use src\LinkyRouting\attributes\Route;
use src\LinkyRouting\Responses\BinaryFileResponse;
use src\LinkyRouting\Responses\View;

#[MvcController]
class DefaultController extends AppController
{
    #[HttpGet]
    #[Route("index")]
    public function index(): View
    {
        return new View('index');
    }

    #[HttpGet]
    #[Route("favicon.ico")]
    public function favicon(): BinaryFileResponse
    {
        $faviconPath = "public/assets/favicon.ico";
        
        if(!file_exists($faviconPath))
            throw new NotFoundException("Favicon not found");

        return new BinaryFileResponse($faviconPath);
    }
}
