<?php

namespace src\Controllers;

use src\Handlers\UserSessionHandler;
use src\LinkyRouting\attributes\controller\MvcController;
use src\LinkyRouting\attributes\httpMethod\HttpGet;
use src\LinkyRouting\attributes\Route;
use src\LinkyRouting\Responses\Redirect;
use src\LinkyRouting\Responses\View;

#[MvcController]
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
    public function dashboard(): View | Redirect
    {
        if (!$this->sessionHandler->isSessionSet()) {
            return new Redirect("login");
        }

        return new View('dashboard');
    }
}