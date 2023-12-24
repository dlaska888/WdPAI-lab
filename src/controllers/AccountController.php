<?php

namespace src\Controllers;

use src\Enums\UserRole;
use src\Handlers\UserSessionHandler;
use src\LinkyRouting\attributes\controller\ApiController;
use src\Models\Entities\File;
use src\Repos\FileRepo;
use src\Repos\UserRepo;
use src\LinkyRouting\attributes\authorization\Authorize;
use src\LinkyRouting\attributes\controller\Controller;
use src\LinkyRouting\attributes\httpMethod\HttpDelete;
use src\LinkyRouting\attributes\httpMethod\HttpGet;
use src\LinkyRouting\attributes\httpMethod\HttpPost;
use src\LinkyRouting\attributes\httpMethod\HttpPut;
use src\LinkyRouting\attributes\Route;
use src\LinkyRouting\enums\HttpStatusCode;
use src\LinkyRouting\Responses\Json; // Assuming you have a Json class for responses
use src\Validators\FileValidator;
use src\Validators\UpdatePasswordValidator;
use src\Validators\UpdateUserNameValidator;

#[ApiController]
#[Authorize([UserRole::NORMAL->value, UserRole::ADMIN->value])]
class AccountController extends AppController
{
    private UserRepo $userRepo;
    private UserSessionHandler $sessionHandler;
    private FileRepo $fileRepo;

    private const UPLOAD_DIRECTORY = 'uploads/';

    public function __construct()
    {
        parent::__construct();
        $this->userRepo = new UserRepo();
        $this->fileRepo = new FileRepo();
        $this->sessionHandler = new UserSessionHandler();
    }

    #[HttpGet]
    #[Route("account")]
    public function getAccountDetails(): Json
    {
        $userId = $this->sessionHandler->getUserId();
        $user = $this->userRepo->findById($userId);

        if (!$user) {
            return new Json("User not found", HttpStatusCode::NOT_FOUND);
        }

        return new Json($user);
    }

    #[HttpGet]
    #[Route("account/profile-picture")]
    public function getProfilePicture(): Json
    {
        $user = $this->userRepo->findById($this->sessionHandler->getUserId());

        if (!$user) {
            return new Json("User not found", HttpStatusCode::NOT_FOUND);
        }

        $file = $this->findProfilePicture($user);

        if ($file === null) {
            return new Json("User picture not found", HttpStatusCode::NOT_FOUND);
        }

        $filePath = self::UPLOAD_DIRECTORY . $file->name;

        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $file->name);
        header("Content-Length: " . filesize($filePath));

        readfile($filePath);

        return new Json(); // Return an empty JSON response after serving the file.
    }

    #[HttpPost]
    #[Route("account/profile-picture")]
    public function uploadProfilePicture(): Json
    {
        if (!array_key_exists("file", $_FILES)) {
            return new Json("No file uploaded", HttpStatusCode::BAD_REQUEST);
        }

        $validationResult = $this->getValidationResult($_FILES['file'], FileValidator::class);
        if(!$validationResult->isSuccess())
            return new Json($validationResult, HttpStatusCode::BAD_REQUEST);
            
        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            return new Json("No file uploaded", HttpStatusCode::BAD_REQUEST);
        }

        $userId = $this->sessionHandler->getUserId();
        $user = $this->userRepo->findById($userId);
        $oldPicture = $this->findProfilePicture($user);

        if ($oldPicture !== null) {
            $this->removeProfilePicture($oldPicture);
        }

        $fileName = $userId . "_" . $_FILES['file']['name'];
        $file = new File($fileName);
        $this->fileRepo->insert($file);

        move_uploaded_file(
            $_FILES['file']['tmp_name'],
            self::UPLOAD_DIRECTORY . $fileName
        );

        $user->profile_picture_id = $file->file_id;

        return new Json($this->userRepo->update($user));
    }

    #[HttpDelete]
    #[Route("account/profile-picture")]
    public function deleteProfilePicture(): Json
    {
        $user = $this->userRepo->findById($this->sessionHandler->getUserId());

        if (!$user) {
            return new Json("User not found", HttpStatusCode::NOT_FOUND);
        }

        $file = $this->findProfilePicture($user);

        if ($file === null) {
            return new Json("User picture not found", HttpStatusCode::NOT_FOUND);
        }

        return new Json($this->removeProfilePicture($file));
    }

    #[HttpPut]
    #[Route("account/change-username")]
    public function changeUsername(): Json
    {
        $userId = $this->sessionHandler->getUserId();
        $user = $this->userRepo->findById($userId);
        
        $requestData = $this->getRequestBody();

        $validationResult = $this->getValidationResult($requestData, UpdateUserNameValidator::class);
        if(!$validationResult->isSuccess())
            return new Json($validationResult, HttpStatusCode::BAD_REQUEST);

        if ($this->userRepo->findByUserName($requestData['userName'])) {
            return new Json("This username is already taken", HttpStatusCode::BAD_REQUEST);
        }

        $user->user_name = $requestData['userName'];

        return new Json($this->userRepo->update($user));
    }

    #[HttpPut]
    #[Route("account/change-password")]
    public function changePassword(): Json
    {
        $userId = $this->sessionHandler->getUserId();
        $user = $this->userRepo->findById($userId);

        $requestData = $this->getRequestBody();

        $validationResult = $this->getValidationResult($requestData, UpdatePasswordValidator::class);
        if(!$validationResult->isSuccess())
            return new Json($validationResult, HttpStatusCode::BAD_REQUEST);

        $password = $requestData['password'];
        $newPassword = $requestData['newPassword'];

        if (!password_verify($password, $user->password_hash)) {
            return new Json("Invalid password", HttpStatusCode::UNAUTHORIZED);
        }

        $user->password_hash = password_hash($newPassword, PASSWORD_BCRYPT);

        return new Json($this->userRepo->update($user));
    }

    private function findProfilePicture($user): ?File
    {
        if ($user->profile_picture_id === null) {
            return null;
        }

        $file = $this->fileRepo->findById($user->profile_picture_id);

        if ($file === null) {
            return null;
        }

        $filePath = self::UPLOAD_DIRECTORY . $file->name;

        if (!file_exists($filePath)) {
            return null;
        }

        return $file instanceof File ? $file : null;
    }

    private function removeProfilePicture(File $file): bool
    {
        $filePath = self::UPLOAD_DIRECTORY . $file->name;

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return $this->fileRepo->delete($file->file_id);
    }
}
