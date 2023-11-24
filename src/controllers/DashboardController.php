<?php

require_once "src/controllers/DefaultController.php";
require_once "src/handlers/UserSessionHandler.php";

class DashboardController extends DefaultController
{
    private UserRepo $userRepo;
    private UserSessionHandler $sessionHandler;
    public function __construct()
    {
        parent::__construct();
        $this->userRepo = new UserRepo();
        $this->sessionHandler = new UserSessionHandler();
    }

    public function dashboard(): void
    {
        if(!$this->sessionHandler->isSessionSet()){
            header("Location: login");
            exit();
        }
        
        $this->render('dashboard');
    }
}