<?php

namespace src\Controllers;

use src\Exceptions\BadRequestException;
use src\Exceptions\UnauthorizedException;
use src\Handlers\UserSessionHandler;
use src\LinkyRouting\Attributes\Controller\MvcController;
use src\LinkyRouting\Attributes\HttpMethod\HttpGet;
use src\LinkyRouting\Attributes\HttpMethod\HttpPost;
use src\LinkyRouting\Attributes\Route;
use src\LinkyRouting\Responses\Redirect;
use src\LinkyRouting\Responses\View;
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
    public function getLoginPage(): View|Redirect
    {
        if ($this->sessionHandler->isSessionSet()) {
            return new Redirect('dashboard');
        }

        return new View('login');
    }

    #[HttpPost]
    #[Route("login")]
    public function login(): View|Redirect
    {
        $this->validateRequestData($_POST, LoginValidator::class);

        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = $this->userRepo->findByEmail($email) ?? $this->userRepo->findByUserName($email);

        if (!$user || !password_verify($password, $user->passwordHash)) {
            throw new UnauthorizedException("Invalid credentials");
        }

        $this->sessionHandler->setSession($user);
        return new Redirect('dashboard');
    }

    #[HttpGet]
    #[Route("register")]
    public function getRegisterPage(): View
    {
        return new View('register');
    }

    #[HttpPost]
    #[Route("register")]
    public function register(): View|Redirect
    {
        $this->validateRequestData($_POST, RegisterValidator::class);

        $email = $_POST['email'];
        $userName = $_POST['userName'];
        $password = $_POST['password'];

        $user = $this->userRepo->findByEmail($email) ?? $this->userRepo->findByUserName($email);
        
        if($user){
            throw new BadRequestException("User already exists");
        }

        $user = new LinkyUser();
        $user->email = $email;
        $user->userName = $userName;
        $user->passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $this->userRepo->insert($user);

        $linkGroup = new LinkGroup();
        $linkGroup->userId = $user->id;
        $linkGroup->name = "Favourites";

        $this->linkGroupRepo->insert($linkGroup);

        $this->sessionHandler->setSession($user);

        return new Redirect('dashboard');
    }

    #[HttpGet]
    #[Route('logout')]
    public function logout(): Redirect
    {
        $this->sessionHandler->unsetSession();
        return new Redirect('login');
    }
}
