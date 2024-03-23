<?php

namespace LinkyApp\Controllers;

use LinkyApp\Handlers\UserSessionHandler;
use LinkyApp\LinkyRouting\Attributes\Controller\MvcController;
use LinkyApp\LinkyRouting\Attributes\HttpMethod\HttpGet;
use LinkyApp\LinkyRouting\Attributes\Route;
use LinkyApp\LinkyRouting\Responses\Redirect;
use LinkyApp\LinkyRouting\Responses\View;

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