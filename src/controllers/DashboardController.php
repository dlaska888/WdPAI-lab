<?php

namespace src\Controllers;

use src\attributes\controller\MvcController;
use src\Attributes\httpMethod\HttpGet;
use src\Attributes\Route;
use src\Handlers\UserSessionHandler;

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
    public function dashboard(): void
    {
        if (!$this->sessionHandler->isSessionSet()) {
            $this->redirect("login");
        }

        $this->render("dashboard");
    }
}