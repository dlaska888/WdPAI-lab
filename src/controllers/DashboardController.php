<?php

namespace src\Controllers;

use src\Handlers\UserSessionHandler;
use src\routing\attributes\controller\Controller;
use src\routing\attributes\httpMethod\HttpGet;
use src\routing\attributes\Route;
use src\routing\responses\View;

#[Controller]
class DashboardController extends DefaultController
{
    private UserSessionHandler $sessionHandler;

    public function __construct()
    {
        parent::__construct();
        $this->sessionHandler = new UserSessionHandler();
    }

    #[HttpGet]
    #[Route("dashboard")]
    public function dashboard(): View
    {
        if (!$this->sessionHandler->isSessionSet()) {
            $this->redirect("login");
        }

        return new View('dashboard');
    }
}