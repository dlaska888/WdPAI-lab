<?php

namespace src\Controllers;

use src\Attributes\ApiController;
use src\Attributes\Route;
use src\Enums\GroupPermissionLevel;
use src\Enums\HttpStatusCode;
use src\Handlers\UserSessionHandler;
use src\Models\Link;
use src\Repos\LinkGroupRepo;
use src\Repos\LinkRepo;

#[ApiController]
class LinkController extends AppController
{
    private LinkGroupRepo $linkGroupRepo;
    private LinkRepo $linkRepo;
    private UserSessionHandler $sessionHandler;

    public function __construct()
    {
        parent::__construct();
        $this->linkGroupRepo = new LinkGroupRepo();
        $this->linkRepo = new LinkRepo();
        $this->sessionHandler = new UserSessionHandler();
    }

    #[Route("link")]
    public function link(string $id): void
    {
        if (!$this->sessionHandler->isSessionSet()) {
            $this->response(HttpStatusCode::UNAUTHORIZED, "User has to be logged in");
        }

        $userId = $this->sessionHandler->getUserId();

        if ($this->isGet()) {
            $link = $this->linkRepo->findById($id);

            if (!$link)
                $this->response(HttpStatusCode::NOT_FOUND, "No link with such id");
            

            if (!$this->checkGroupAccess($userId, $link->link_group_id, GroupPermissionLevel::READ))
                $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to access this link");
            

            $this->response(HttpStatusCode::OK, $link);
        }

        if ($this->isPost()) {
            $linkData = $this->getRequestBody();
            if ($linkData === null || !array_key_exists('title', $linkData) || !array_key_exists('url', $linkData)) {
                $this->response(HttpStatusCode::BAD_REQUEST, "Invalid request body");
            }

            // Check if the user has access to the link group
            if (!$this->checkGroupAccess($userId, $linkData['link_group_id'], GroupPermissionLevel::WRITE)) {
                $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to add a link to this group");
            }

            $link = new Link($linkData['link_group_id'], $linkData['title'], $linkData['url']);
            $this->response(HttpStatusCode::CREATED, $this->linkRepo->insert($link));
        }

        if ($this->isPut()) {
            $link = $this->linkRepo->findById($id);

            if (!$link) {
                $this->response(HttpStatusCode::NOT_FOUND, "No link with such id");
            }

            if (!$this->checkGroupAccess($userId, $link->link_group_id, GroupPermissionLevel::WRITE)) {
                $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to edit this link");
            }

            $linkData = $this->getRequestBody();
            if ($linkData === null) {
                $this->response(HttpStatusCode::BAD_REQUEST, "Invalid request body");
            }

            $link->title = $linkData['title'] ?? $link->title;
            $link->url = $linkData['url'] ?? $link->url;

            $this->response(HttpStatusCode::OK, $this->linkRepo->update($link));
        }

        if ($this->isDelete()) {
            $link = $this->linkRepo->findById($id);

            if (!$link) {
                $this->response(HttpStatusCode::NOT_FOUND, "No link with such id");
            }

            if (!$this->checkGroupAccess($userId, $link->link_group_id, GroupPermissionLevel::WRITE)) {
                $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to delete this link");
            }

            $this->linkRepo->delete($id);
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
