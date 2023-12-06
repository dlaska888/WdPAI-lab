<?php

namespace src\Controllers;

use src\Attributes\ApiController;
use src\Attributes\Route;
use src\Enums\GroupPermissionLevel;
use src\Enums\HttpStatusCode;
use src\Handlers\UserSessionHandler;
use src\Models\LinkGroupShare;
use src\Repos\LinkGroupRepo;
use src\Repos\LinkGroupShareRepo;
use DateTime;

#[ApiController]
class LinkGroupShareController extends AppController
{
    private LinkGroupRepo $linkGroupRepo;
    private LinkGroupShareRepo $linkGroupShareRepo;
    private UserSessionHandler $sessionHandler;

    public function __construct()
    {
        parent::__construct();
        $this->linkGroupRepo = new LinkGroupRepo();
        $this->linkGroupShareRepo = new LinkGroupShareRepo();
        $this->sessionHandler = new UserSessionHandler();
    }

    #[Route("linkGroupShare")]
    public function linkGroupShare(string $id): void
    {
        if (!$this->sessionHandler->isSessionSet())
            $this->response(HttpStatusCode::UNAUTHORIZED, "Invalid session");

        $userId = $this->sessionHandler->getUserId();

        if ($this->isPost()) {
            $linkGroupShareData = $this->getRequestBody();

            if ($linkGroupShareData === null ||
                !array_key_exists('user_id', $linkGroupShareData) ||
                !array_key_exists('link_group_id', $linkGroupShareData) ||
                !array_key_exists('permission', $linkGroupShareData) ||
                !GroupPermissionLevel::tryFrom($linkGroupShareData['permission'])
            )
                $this->response(HttpStatusCode::BAD_REQUEST, "Invalid request body");

            $shareToUserId = $linkGroupShareData['user_id'];
            $linkGroupId = $linkGroupShareData['link_group_id'];
            $permissionLevel = GroupPermissionLevel::from($linkGroupShareData['permission']);

            // Check if the user has access to the link group
            if (!$this->checkGroupAccess($userId, $linkGroupId, $permissionLevel))
                $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to share this link group.");


            $linkGroupShare = new LinkGroupShare($shareToUserId, $linkGroupId, new DateTime(), $permissionLevel);
            $this->response(HttpStatusCode::CREATED, $this->linkGroupShareRepo->insert($linkGroupShare));
        }

        if ($this->isPut()) {
            $linkGroupShare = $this->linkGroupShareRepo->findById($id);

            if (!$linkGroupShare)
                $this->response(HttpStatusCode::NOT_FOUND, "No link group share with such id");
            
            $linkGroupShareData = $this->getRequestBody();

            if ($linkGroupShareData === null ||
                !array_key_exists('permission', $linkGroupShareData) ||
                !GroupPermissionLevel::tryFrom($linkGroupShareData['permission'])
            )
                $this->response(HttpStatusCode::BAD_REQUEST, "Invalid request body");
            
            $linkGroup = $this->linkGroupRepo->findById($linkGroupShare->link_group_id);

            if (!$linkGroup)
                $this->response(HttpStatusCode::NOT_FOUND, "No link group with such id");

            if($linkGroup->user_id !== $userId)
                $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to edit this share");

            $permissionLevel = GroupPermissionLevel::from($linkGroupShareData['permission']);
            $linkGroupShare->permission = $permissionLevel;
            
            $this->response(HttpStatusCode::CREATED, $this->linkGroupShareRepo->update($linkGroupShare));
        }


        if ($this->isDelete()) {
            $linkGroupShare = $this->linkGroupShareRepo->findById($id);

            if (!$linkGroupShare)
                $this->response(HttpStatusCode::NOT_FOUND, "No link group share with such id");

            $linkGroup = $this->linkGroupRepo->findById($linkGroupShare->link_group_id);

            if (!$linkGroup)
                $this->response(HttpStatusCode::NOT_FOUND, "No link group with such id");

            if($linkGroup->user_id !== $userId)
                $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to edit this share");

            $this->response(HttpStatusCode::CREATED, $this->linkGroupShareRepo->delete($id));
        }

        // In case of other method
        $this->response(HttpStatusCode::METHOD_NOT_ALLOWED, "Method not allowed.");
    }

    private function checkGroupAccess(string $userId, string $linkGroupId, GroupPermissionLevel $permissionLevel): bool
    {
        $linkGroup = $this->linkGroupRepo->findById($linkGroupId);

        if ($linkGroup->user_id === $userId)
            return true;

        foreach ($linkGroup->groupShares as $share) {
            if ($share->user_id !== $userId)
                continue;

            if ($permissionLevel === GroupPermissionLevel::READ || $share->permission === $permissionLevel)
                return true;

        }

        return false;
    }
}
