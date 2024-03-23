<?php

namespace LinkyApp\Controllers;

use LinkyApp\Exceptions\BadRequestException;
use LinkyApp\Exceptions\UnauthorizedException;
use LinkyApp\Handlers\UserSessionHandler;
use LinkyApp\LinkyRouting\Attributes\Controller\MvcController;
use LinkyApp\LinkyRouting\Attributes\HttpMethod\HttpGet;
use LinkyApp\LinkyRouting\Attributes\HttpMethod\HttpPost;
use LinkyApp\LinkyRouting\Attributes\Route;
use LinkyApp\LinkyRouting\Enums\HttpStatusCode;
use LinkyApp\LinkyRouting\Responses\Json;
use LinkyApp\LinkyRouting\Responses\Redirect;
use LinkyApp\LinkyRouting\Responses\View;
use LinkyApp\Models\Entities\LinkGroup;
use LinkyApp\Models\Entities\LinkyUser;
use LinkyApp\Repos\LinkGroupRepo;
use LinkyApp\Repos\UserRepo;
use LinkyApp\Validators\LoginValidator;
use LinkyApp\Validators\RegisterValidator;

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

        if ($user) {
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
    #[Route("logout")]
    public function logout(): Redirect
    {
        $this->sessionHandler->unsetSession();
        return new Redirect('login');
    }

    #[HttpPost]
    #[Route("refreshSession")]
    public function refreshSession(): Json
    {
        return $this->sessionHandler->refreshSession() ?
            new Json(null, HttpStatusCode::OK) :
            new Json(null, HttpStatusCode::UNAUTHORIZED);
    }
}
