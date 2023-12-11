<?php

namespace src\Controllers;

use DateTime;
use src\attributes\controller\MvcController;
use src\Attributes\httpMethod\HttpGet;
use src\Attributes\httpMethod\HttpPost;
use src\Attributes\Route;
use src\Handlers\UserSessionHandler;
use src\Models\Entities\LinkGroup;
use src\Models\Entities\LinkyUser;
use src\Repos\LinkGroupRepo;
use src\Repos\UserRepo;
use src\Validators\LoginValidator;
use src\Validators\RegisterValidator;

#[MvcController]
class SecurityController extends AppController
{
    private UserRepo $userRepo;
    private LinkGroupRepo $linkGroupRepo;
    private UserSessionHandler $sessionHandler;

    public function __construct()
    {
        parent::__construct();
        $this->userRepo = new UserRepo();
        $this->sessionHandler = new UserSessionHandler();
        $this->linkGroupRepo = new LinkGroupRepo();
    }


    #[HttpGet]
    #[Route("login")]
    public function getLoginPage(): void
    {
        if ($this->sessionHandler->isSessionSet()) {
            $this->redirect("dashboard");
        }

        $this->render('login');
    }

    #[HttpPost]
    #[Route("login")]
    public function login(): void
    {
        $this->validateRequestData($_POST, LoginValidator::class);
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = $this->userRepo->findByEmail($email) ?? $this->userRepo->findByUserName($email);

        if (!$user || !password_verify($password, $user->password_hash)) {
            $this->render('login', ['messages' => ['Invalid credentials']]);
        }

        $this->sessionHandler->setSession($user);
        $this->redirect('dashboard');
    }

    #[HttpGet]
    #[Route("register")]
    public function getRegisterPage(): void
    {
        $this->render('register');
    }

    #[HttpPost]
    #[Route("register")]
    public function register(): void
    {
        $this->validateRequestData($_POST, RegisterValidator::class);
        $email = $_POST['email'];
        $userName = $_POST['userName'];
        $password = $_POST['password'];

        // Check if user already exists
        if ($this->userRepo->findByEmail($email) || $this->userRepo->findByUserName($userName)) {
            $this->render('register', ['messages' => ['User already exists']]);
        }

        $user = new LinkyUser(
            user_name: $userName,
            email: $email,
            password_hash: password_hash($password, PASSWORD_BCRYPT)
        );

        $linkGroup = new LinkGroup(
            user_id: $user->user_id,
            name: 'No Group',
            date_created: new DateTime()
        );

        $this->userRepo->insert($user);
        $this->linkGroupRepo->insert($linkGroup);

        $this->sessionHandler->setSession($user);

        $this->redirect('dashboard');
    }

    #[HttpPost]
    #[Route('logout')]
    public function logout(): void
    {
        $this->sessionHandler->unsetSession();
        $this->redirect('login');
    }

}