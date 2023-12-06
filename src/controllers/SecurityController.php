<?php

namespace src\Controllers;

use src\Attributes\ApiController;
use src\Attributes\Route;
use src\Handlers\UserSessionHandler;
use src\Models\LinkGroup;
use src\Models\LinkyUser;
use src\Repos\LinkGroupRepo;
use src\Repos\UserRepo;
use DateTime;
use JetBrains\PhpStorm\NoReturn;

#[ApiController]
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

    #[Route("login")]
    public function login(): void
    {
        if (!$this->isPost()) {

            if($this->sessionHandler->isSessionSet()){
                header("Location: dashboard");
                exit();
            }

            $this->render('login');
            return;
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        $validationResult = $this->validateLoginData($email, $password);

        if (!$validationResult['valid']) {
            $this->render('login', ['messages' => $validationResult['messages']]);
            return;
        }

        $user = $this->userRepo->findByEmail($email) ?? $this->userRepo->findByUserName($email);

        if (!$user || !password_verify($password, $user->password_hash)) {
            $this->render('login', ['messages' => ['Invalid credentials']]);
            return;
        }

        $this->sessionHandler->setSession($user);

        header("Location: dashboard");
        exit();
    }

    #[Route("register")]
    public function register(): void
    {
        if (!$this->isPost()) {
            $this->render('register');
            return;
        }

        $userName = $_POST['userName'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $passwordConfirm = $_POST['passwordConfirm'];

        $validationResult = $this->validateRegistrationData($userName, $email, $password, $passwordConfirm);

        if (!$validationResult['valid']) {
            $this->render('register', ['messages' => $validationResult['messages']]);
            return;
        }

        // Check if user already exists
        if ($this->userRepo->findByEmail($email) || $this->userRepo->findByUserName($userName)) {
            $this->render('register', ['messages' => ['User already exists']]);
            return;
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

        header("Location: dashboard");
        exit();
    }

    #[NoReturn]
    public function logout(): void
    {
        $this->sessionHandler->unsetSession();
        header("Location: dashboard");
        exit();
    }

    private function validateLoginData(string $email, string $password): array
    {
        $messages = [];

        // Validate email
        if (empty($email)) {
            $messages[] = 'Username or email is required';
        }

        // Validate password
        if (empty($password)) {
            $messages[] = 'Password is required';
        }

        return ['valid' => empty($messages), 'messages' => $messages];
    }

    private function validateRegistrationData(string $userName, string $email, string $password, string $passwordConfirm): array
    {
        $messages = [];

        // Validate username
        if (empty($userName) || strlen($userName) < 3) {
            $messages[] = 'Username must be at least 3 characters long';
        }

        // Validate email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $messages[] = 'Invalid email address';
        }

        // Validate password
        if (empty($password) || strlen($password) < 8) {
            $messages[] = 'Password must be at least 8 characters long';
        }

        // Validate password confirmation
        if ($password !== $passwordConfirm) {
            $messages[] = 'Passwords must be the same\'';
        }

        return ['valid' => empty($messages), 'messages' => $messages];
    }

}