<?php

namespace src\Controllers;

use src\Attributes\ApiController;
use src\Attributes\Route;
use src\Handlers\UserSessionHandler;

#[ApiController]
class DashboardController extends DefaultController
{
    private UserSessionHandler $sessionHandler;
    public function __construct()
    {
        parent::__construct();
        $this->sessionHandler = new UserSessionHandler();
    }

    #[Route("dashboard")]
    public function dashboard(): void
    {
        if(!$this->sessionHandler->isSessionSet()){
            header("Location: login");
            exit();
        }
        
        $this->render('dashboard');
    }
}