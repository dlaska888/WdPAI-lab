<?php

namespace src\Controllers;

use src\Handlers\UserSessionHandler;
use src\LinkyRouting\attributes\controller\Controller;
use src\LinkyRouting\attributes\httpMethod\HttpGet;
use src\LinkyRouting\attributes\Route;
use src\LinkyRouting\Responses\View;

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