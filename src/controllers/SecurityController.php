<?php

namespace src\Controllers;

use DateTime;
use src\exceptions\BadRequestException;
use src\exceptions\NotFoundException;
use src\Handlers\UserSessionHandler;
use src\LinkyRouting\attributes\controller\MvcController;
use src\LinkyRouting\Responses\Redirect;
use src\Models\Entities\LinkGroup;
use src\Models\Entities\LinkyUser;
use src\Repos\LinkGroupRepo;
use src\Repos\UserRepo;
use src\LinkyRouting\attributes\httpMethod\HttpGet;
use src\LinkyRouting\attributes\httpMethod\HttpPost;
use src\LinkyRouting\attributes\Route;
use src\LinkyRouting\enums\HttpStatusCode;
use src\LinkyRouting\Responses\View;
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

        if (!$user || !password_verify($password, $user->password_hash)) {
            return new View('login', ['data' => ['Invalid credentials']], HttpStatusCode::UNAUTHORIZED);
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

        // Check if user already exists
        try {
            if ($this->userRepo->findByEmail($email) || $this->userRepo->findByUserName($userName)) {
                throw new BadRequestException("User already exists");
            }    
        }catch (NotFoundException){
            // Continue if user is not found
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
