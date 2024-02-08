<?php

namespace src\Controllers;

use src\Exceptions\NotFoundException;
use src\LinkyRouting\Attributes\Controller\MvcController;
use src\LinkyRouting\Attributes\HttpMethod\HttpGet;
use src\LinkyRouting\Attributes\Route;
use src\LinkyRouting\Responses\BinaryFile;
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
    public function favicon(): BinaryFile
    {
        $faviconPath = "public/assets/favicon.ico";
        
        if(!file_exists($faviconPath))
            throw new NotFoundException("Favicon not found");

        return new BinaryFile($faviconPath);
    }
}
