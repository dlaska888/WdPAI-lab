<?php

namespace src\Controllers;

use DateTime;
use src\Handlers\UserSessionHandler;
use src\Models\Entities\LinkGroup;
use src\Models\Entities\LinkyUser;
use src\Repos\LinkGroupRepo;
use src\Repos\UserRepo;
use src\LinkyRouting\attributes\controller\Controller;
use src\LinkyRouting\attributes\httpMethod\HttpGet;
use src\LinkyRouting\attributes\httpMethod\HttpPost;
use src\LinkyRouting\attributes\Route;
use src\LinkyRouting\enums\HttpStatusCode;
use src\LinkyRouting\Responses\View;
use src\Validators\LoginValidator;
use src\Validators\RegisterValidator;

#[Controller]
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
    public function getLoginPage(): View
    {
        if ($this->sessionHandler->isSessionSet()) {
            $this->redirect("dashboard");
        }

        return new View('login');
    }

    #[HttpPost]
    #[Route("login")]
    public function login(): ?View
    {
        $validationResult = $this->getValidationResult($_POST, LoginValidator::class);
        if (!$validationResult->isSuccess()) {
            return new View('login', ['messages' => $validationResult->getErrors()], HttpStatusCode::BAD_REQUEST);
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = $this->userRepo->findByEmail($email) ?? $this->userRepo->findByUserName($email);

        if (!$user || !password_verify($password, $user->password_hash)) {
            return new View('login', ['messages' => ['Invalid credentials']], HttpStatusCode::UNAUTHORIZED);
        }

        $this->sessionHandler->setSession($user);
        $this->redirect('dashboard');
        
        return null;
    }

    #[HttpGet]
    #[Route("register")]
    public function getRegisterPage(): View
    {
        return new View('register');
    }

    #[HttpPost]
    #[Route("register")]
    public function register(): ?View
    {
        $validationResult = $this->getValidationResult($_POST, RegisterValidator::class);
        if (!$validationResult->isSuccess()) {
            return new View('register', ['messages' => $validationResult->getErrors()], HttpStatusCode::BAD_REQUEST);
        }

        $email = $_POST['email'];
        $userName = $_POST['userName'];
        $password = $_POST['password'];

        // Check if user already exists
        if ($this->userRepo->findByEmail($email) || $this->userRepo->findByUserName($userName)) {
            return new View('register', ['messages' => ['User already exists']], HttpStatusCode::BAD_REQUEST);
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
        
        return null;
    }

    #[HttpGet]
    #[Route('logout')]
    public function logout(): void
    {
        $this->sessionHandler->unsetSession();
        $this->redirect('login');
    }
}
