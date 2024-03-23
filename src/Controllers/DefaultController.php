<?php

namespace LinkyApp\Controllers;

use LinkyApp\Exceptions\NotFoundException;
use LinkyApp\LinkyRouting\Attributes\Controller\MvcController;
use LinkyApp\LinkyRouting\Attributes\HttpMethod\HttpGet;
use LinkyApp\LinkyRouting\Attributes\Route;
use LinkyApp\LinkyRouting\Responses\BinaryFile;
use LinkyApp\LinkyRouting\Responses\View;

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
