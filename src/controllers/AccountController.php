<?php

namespace src\Controllers;

use src\Attributes\authorization\Authorize;
use src\Attributes\controller\ApiController;
use src\Attributes\httpMethod\HttpDelete;
use src\Attributes\httpMethod\HttpGet;
use src\Attributes\httpMethod\HttpPost;
use src\Attributes\httpMethod\HttpPut;
use src\Attributes\Route;
use src\Enums\HttpStatusCode;
use src\Enums\UserRole;
use src\Handlers\UserSessionHandler;
use src\Models\Entities\File;
use src\Repos\FileRepo;
use src\Repos\UserRepo;
use src\Validators\FileValidator;
use src\Validators\UpdatePasswordValidator;
use src\Validators\UpdateUserNameValidator;

#[ApiController]
#[Authorize(UserRole::NORMAL)]
class AccountController extends AppController
{
    private UserRepo $userRepo;
    private UserSessionHandler $sessionHandler;
    private FileRepo $fileRepo;

    // TODO implement better way of keeping files securely
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
    public function getAccountDetails(): void
    {
        $userId = $this->sessionHandler->getUserId();
        $user = $this->userRepo->findById($userId);

        if (!$user) {
            $this->response(HttpStatusCode::NOT_FOUND, "User not found");
        }

        // You might want to exclude sensitive information like password hash before responding
        $this->response(HttpStatusCode::OK, $user);
    }

    #[HttpGet]
    #[Route("account/profile-picture")]
    public function getProfilePicture(): void
    {
        $user = $this->userRepo->findById($this->sessionHandler->getUserId());

        if (!$user) {
            $this->response(HttpStatusCode::NOT_FOUND, "User not found");
        }

        $file = $this->findProfilePicture($user);
        if($file === null){
            $this->response(HttpStatusCode::NOT_FOUND, "User picture not found");
        }
        
        $filePath = self::UPLOAD_DIRECTORY . $file->name;

        // Set appropriate headers for file download
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $file->name);
        header("Content-Length: " . filesize($filePath));

        // Output the file content
        readfile($filePath);
    }


    #[HttpPost]
    #[Route("account/profile-picture")]
    public function uploadProfilePicture(): void
    {
        if (!array_key_exists("file", $_FILES))
            $this->response(HttpStatusCode::BAD_REQUEST, "No file uploaded");

        $this->validationResponse($_FILES['file'], FileValidator::class);

        if (!is_uploaded_file($_FILES['file']['tmp_name']))
            $this->response(HttpStatusCode::BAD_REQUEST, "No file uploaded");

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
        $this->response(HttpStatusCode::OK, $this->userRepo->update($user));
    }

    #[HttpDelete]
    #[Route("account/profile-picture")]
    public function deleteProfilePicture(): void
    {
        $user = $this->userRepo->findById($this->sessionHandler->getUserId());

        if (!$user) {
            $this->response(HttpStatusCode::NOT_FOUND, "User not found");
        }

        $file = $this->findProfilePicture($user);
        if($file === null){
            $this->response(HttpStatusCode::NOT_FOUND, "User picture not found");
        }
        
        $this->response(HttpStatusCode::OK, $this->removeProfilePicture($file));
    }

    #[HttpPut]
    #[Route("account/change-username")]
    public function changeUsername(): void
    {
        $userId = $this->sessionHandler->getUserId();
        $user = $this->userRepo->findById($userId);

        $requestData = $this->getRequestBody();
        $this->validationResponse($requestData, UpdateUserNameValidator::class);

        if ($this->userRepo->findByUserName($requestData['userName']))
            $this->response(HttpStatusCode::BAD_REQUEST, "This username is already taken");

        $user->user_name = $requestData['userName'];

        $this->response(HttpStatusCode::OK, $this->userRepo->update($user));
    }

    #[HttpPut]
    #[Route("account/change-password")]
    public function changePassword(): void
    {
        $userId = $this->sessionHandler->getUserId();
        $user = $this->userRepo->findById($userId);

        $requestData = $this->getRequestBody();
        $this->validationResponse($requestData, UpdatePasswordValidator::class);

        $password = $requestData['password'];
        $newPassword = $requestData['newPassword']; // Checking passwordConfirm is done in validator

        if (!password_verify($password, $user->password_hash))
            $this->response(HttpStatusCode::UNAUTHORIZED, "Invalid password");

        $user->password_hash = password_hash($newPassword, PASSWORD_BCRYPT);

        $this->response(HttpStatusCode::OK, $this->userRepo->update($user));
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
        // Remove the file from the server
        $filePath = self::UPLOAD_DIRECTORY . $file->name;

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Remove the file record from the database
        return $this->fileRepo->delete($file->file_id);
    }

}

