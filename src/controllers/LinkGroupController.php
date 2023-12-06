<?php

namespace src\Controllers;

use src\Attributes\ApiController;
use src\Attributes\Route;
use src\Enums\GroupPermissionLevel;
use src\Enums\HttpStatusCode;
use src\Handlers\UserSessionHandler;
use src\Models\LinkGroup;
use src\Repos\LinkGroupRepo;

#[ApiController]
class LinkGroupController extends AppController
{
    private LinkGroupRepo $linkGroupRepo;
    private UserSessionHandler $sessionHandler;

    public function __construct()
    {
        parent::__construct();
        $this->linkGroupRepo = new LinkGroupRepo();
        $this->sessionHandler = new UserSessionHandler();
    }

    #[Route("linkGroup")]
    public function linkGroup(string $id): void
    {
        if (!$this->sessionHandler->isSessionSet())
            $this->response(HttpStatusCode::UNAUTHORIZED, "Invalid session");

        $userId = $this->sessionHandler->getUserId();

        if ($this->isGet()) {
            if(!$id)
                $this->response(HttpStatusCode::OK, $this->linkGroupRepo->findAllUserGroups($userId));
                
            if($id === 'shared')
                $this->response(HttpStatusCode::OK, $this->linkGroupRepo->findAllUserSharedGroups($userId));
            
            $linkGroup = $this->linkGroupRepo->findById($id);

            if (!$linkGroup)
                $this->response(HttpStatusCode::NOT_FOUND, "No link group with such id");

            if (!$this->checkGroupAccess($userId, $linkGroup->link_group_id, GroupPermissionLevel::READ))
                $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to access this link group");

            $this->response(HttpStatusCode::OK, $linkGroup);
        }

        if ($this->isPost()) {
            $userId = $this->sessionHandler->getUserId();
            
            $linkGroupData = $this->getRequestBody();
            if ($linkGroupData === null || !array_key_exists('name', $linkGroupData))
                $this->response(HttpStatusCode::BAD_REQUEST, "Invalid request body");

            $linkGroup = new LinkGroup($userId, $linkGroupData['name']);
            $this->response(HttpStatusCode::CREATED, $this->linkGroupRepo->insert($linkGroup));
        }

        if ($this->isPut()) {
            $linkGroup = $this->linkGroupRepo->findById($id);

            if (!$linkGroup)
                $this->response(HttpStatusCode::NOT_FOUND, "No link group with such id");

            if (!$this->checkGroupAccess($userId, $linkGroup->link_group_id, GroupPermissionLevel::WRITE))
                $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to edit this link group");

            // Update link group data based on request body
            $linkGroupData = $this->getRequestBody();
            if ($linkGroupData === null)
                $this->response(HttpStatusCode::BAD_REQUEST, "Invalid request body");

            $linkGroup->name = $linkGroupData['name'] ?? $linkGroup->name;
            
            $this->response(HttpStatusCode::OK, $this->linkGroupRepo->update($linkGroup));
        }

        if ($this->isDelete()) {
            $linkGroup = $this->linkGroupRepo->findById($id);

            if (!$linkGroup)
                $this->response(HttpStatusCode::NOT_FOUND, "No link group with such id");

            if ($linkGroup->user_id !== $userId)
                $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to delete this link group");

            // Delete the link group
            $this->linkGroupRepo->delete($id);
            $this->response(HttpStatusCode::OK);
        }

        // In case of other method
        $this->response(HttpStatusCode::METHOD_NOT_ALLOWED, "Method not allowed");
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
