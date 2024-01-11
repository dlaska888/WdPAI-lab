<?php

namespace src\Controllers;

use src\Enums\UserRole;
use src\exceptions\BadRequestException;
use src\exceptions\NotFoundException;
use src\Handlers\UserSessionHandler;
use src\LinkyRouting\attributes\controller\ApiController;
use src\Models\Entities\File;
use src\Models\Entities\Link;
use src\Models\Entities\LinkyUser;
use src\Repos\FileRepo;
use src\Repos\UserRepo;
use src\LinkyRouting\attributes\authorization\Authorize;
use src\LinkyRouting\attributes\httpMethod\HttpDelete;
use src\LinkyRouting\attributes\httpMethod\HttpGet;
use src\LinkyRouting\attributes\httpMethod\HttpPost;
use src\LinkyRouting\attributes\httpMethod\HttpPut;
use src\LinkyRouting\attributes\Route;
use src\LinkyRouting\enums\HttpStatusCode;
use src\LinkyRouting\Responses\Json;
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

        return new Json($user);
    }

    #[HttpGet]
    #[Route("account/profile-picture")]
    public function getProfilePicture(): Json
    {
        $user = $this->userRepo->findById($this->sessionHandler->getUserId());
        $file = $this->findProfilePicture($user);

        $filePath = self::UPLOAD_DIRECTORY . $file->name;

        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $file->name);
        header("Content-Length: " . filesize($filePath));

        readfile($filePath);

        return new Json();
    }

    #[HttpPost]
    #[Route("account/profile-picture")]
    public function uploadProfilePicture(): Json
    {
        if (!array_key_exists("file", $_FILES)) {
            throw new BadRequestException("No file uploaded");
        }

        $this->validateRequestData($_FILES['file'], FileValidator::class);

        if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
            throw new BadRequestException("No file uploaded");
        }

        $userId = $this->sessionHandler->getUserId();
        $user = $this->userRepo->findById($userId);
        
        try{
            $oldPicture = $this->findProfilePicture($user);
            $this->removeProfilePicture($oldPicture);
        }catch (NotFoundException){
        }

        $fileName = $userId . "_" . $_FILES['file']['name'];
        $file = new File();
        $file->name = $fileName;

        $this->fileRepo->insert($file);

        move_uploaded_file(
            $_FILES['file']['tmp_name'],
            self::UPLOAD_DIRECTORY . $fileName
        );

        $user->profilePictureId = $file->id;

        return new Json($this->userRepo->update($user));
    }

    #[HttpDelete]
    #[Route("account/profile-picture")]
    public function deleteProfilePicture(): Json
    {
        $user = $this->userRepo->findById($this->sessionHandler->getUserId());
        $file = $this->findProfilePicture($user);
        
        return new Json($this->removeProfilePicture($file));
    }

    #[HttpPut]
    #[Route("account/change-username")]
    public function changeUsername(): Json
    {
        $userId = $this->sessionHandler->getUserId();
        $user = $this->userRepo->findById($userId);

        $requestData = $this->getRequestBody();
        $this->validateRequestData($requestData, UpdateUserNameValidator::class);

        try {
            $this->userRepo->findByUserName($requestData['userName']);
            throw new BadRequestException("Username is already taken");
        }
        catch (NotFoundException) {
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
        $this->validateRequestData($requestData, UpdatePasswordValidator::class);

        $password = $requestData['password'];
        $newPassword = $requestData['newPassword'];

        if (!password_verify($password, $user->password_hash)) {
            throw new BadRequestException("Invalid password");
        }

        $user->password_hash = password_hash($newPassword, PASSWORD_BCRYPT);

        return new Json($this->userRepo->update($user));
    }

    private function findProfilePicture(LinkyUser $user): ?File
    {
        if ($user->profilePictureId === null) {
            throw new NotFoundException("User picture not found");
        }

        $file = $this->fileRepo->findById($user->profilePictureId);
        $filePath = self::UPLOAD_DIRECTORY . $file->name;

        if (!file_exists($filePath)) {
            throw new NotFoundException("User picture not found");
        }

        return $file;
    }

    private function removeProfilePicture(File $file): bool
    {
        $filePath = self::UPLOAD_DIRECTORY . $file->name;

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return $this->fileRepo->delete($file->id);
    }
}
