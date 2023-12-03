<?php

require_once "src/controllers/AppController.php";
require_once "src/enums/GroupPermissionLevel.php";

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

    public function linkGroup(string $id): void
    {
        if (!$this->sessionHandler->isSessionSet())
            $this->response(HttpStatusCode::UNAUTHORIZED, "User has to be logged in.");

        if ($this->isGet()) {
            $userId = $this->sessionHandler->getUserId();
            $linkGroup = $this->linkGroupRepo->findById($id);

            if (!$linkGroup)
                $this->response(HttpStatusCode::NOT_FOUND, "No link group with such id.");

            if (!$this->checkGroupAccess($userId, $linkGroup->link_group_id, GroupPermissionLevel::READ))
                $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to access this link group.");

            $this->response(HttpStatusCode::OK, $linkGroup);
        }

        if ($this->isPost()) {
            $userId = $this->sessionHandler->getUserId();
            
            $linkGroupData = $this->getRequestBody();
            if ($linkGroupData === null || !array_key_exists('name', $linkGroupData))
                $this->response(HttpStatusCode::BAD_REQUEST, "Invalid request body.");

            $linkGroup = new LinkGroup($userId, $linkGroupData['name']);
            $this->response(HttpStatusCode::CREATED, $this->linkGroupRepo->insert($linkGroup));
        }

        if ($this->isPut()) {
            $userId = $this->sessionHandler->getUserId();
            $linkGroup = $this->linkGroupRepo->findById($id);

            if (!$linkGroup)
                $this->response(HttpStatusCode::NOT_FOUND, "No link group with such id.");

            if (!$this->checkGroupAccess($userId, $linkGroup->link_group_id, GroupPermissionLevel::WRITE))
                $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to edit this link group");

            // Update link group data based on request body
            $linkGroupData = $this->getRequestBody();
            if ($linkGroupData === null)
                $this->response(HttpStatusCode::BAD_REQUEST, "Invalid request body.");

            $linkGroup->name = $linkGroupData['name'] ?? $linkGroup->name;
            
            $this->response(HttpStatusCode::OK, $this->linkGroupRepo->update($linkGroup));
        }


        if ($this->isDelete()) {
            $userId = $this->sessionHandler->getUserId();
            $linkGroup = $this->linkGroupRepo->findById($id);

            if (!$linkGroup)
                $this->response(HttpStatusCode::NOT_FOUND, "No link group with such id.");

            if ($linkGroup->user_id !== $userId)
                $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to delete this link group");

            // Delete the link group
            $this->linkGroupRepo->delete($id);
            $this->response(HttpStatusCode::OK);
        }

        // In case of other method
        $this->response(HttpStatusCode::METHOD_NOT_ALLOWED, "Method not allowed.");
    }

    private function checkGroupAccess(string $userId, string $linkGroupId, GroupPermissionLevel $permissionLevel) : bool
    {
        $linkGroup = $this->linkGroupRepo->findById($linkGroupId);

        // Check if user is owner of linkGroup
        if ($linkGroup->user_id === $userId)
            return true;

        // Check if user have access to linkGroup
        foreach ($linkGroup->groupShares as $share) {
            if ($share->user_id === $userId && $share->permission === $permissionLevel)
                return true;
        }

        return false;
    }
}
