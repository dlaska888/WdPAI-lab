<?php

namespace src\Controllers;

use DateTime;
use src\Attributes\ApiController;
use src\Attributes\httpMethod\HttpDelete;
use src\Attributes\httpMethod\HttpGet;
use src\Attributes\httpMethod\HttpPost;
use src\Attributes\httpMethod\HttpPut;
use src\Attributes\Route;
use src\Enums\GroupPermissionLevel;
use src\Enums\HttpStatusCode;
use src\Handlers\UserSessionHandler;
use src\Models\Entities\Link;
use src\Models\Entities\LinkGroup;
use src\Models\Entities\LinkGroupShare;
use src\Repos\LinkGroupRepo;
use src\Repos\LinkGroupShareRepo;
use src\Repos\LinkRepo;

#[ApiController]
class LinkController extends AppController
{
    private LinkRepo $linkRepo;
    private LinkGroupRepo $linkGroupRepo;
    private LinkGroupShareRepo $linkGroupShareRepo;
    private UserSessionHandler $sessionHandler;

    public function __construct()
    {
        parent::__construct();
        $this->linkRepo = new LinkRepo();
        $this->linkGroupRepo = new LinkGroupRepo();
        $this->linkGroupShareRepo = new LinkGroupShareRepo();
        $this->sessionHandler = new UserSessionHandler();

        if (!$this->sessionHandler->isSessionSet()) {
            $this->response(HttpStatusCode::UNAUTHORIZED, "User has to be logged in");
        }
    }

    #[HttpGet]
    #[Route("linkGroup/{groupId}/link/{linkId}")]
    public function getLink(string $groupId, string $linkId): void
    {
        $link = $this->findLink($groupId, $linkId);

        if (!$link) {
            $this->response(HttpStatusCode::NOT_FOUND, "No link with such id in this group");
        }

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(),
            $groupId,
            GroupPermissionLevel::READ)) {
            $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to access this link");
        }

        $this->response(HttpStatusCode::OK, $link);
    }

    #[HttpPost]
    #[Route("linkGroup/{groupId}/link")]
    public function addLink(string $groupId): void
    {
        $linkData = $this->getRequestBody();
        if ($linkData === null || !array_key_exists('title', $linkData) ||
            !array_key_exists('url', $linkData)) {
            $this->response(HttpStatusCode::BAD_REQUEST, "Invalid request body");
        }

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(),
            $groupId,
            GroupPermissionLevel::WRITE)) {
            $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to create link in this group");
        }

        $link = new Link($groupId, $linkData['title'], $linkData['url']);
        $this->response(HttpStatusCode::CREATED, $this->linkRepo->insert($link));
    }

    #[HttpPut]
    #[Route("linkGroup/{groupId}/link/{linkId}")]
    public function updateLink(string $groupId, string $linkId): void
    {
        $link = $this->findLink($groupId, $linkId);

        if (!$link) {
            $this->response(HttpStatusCode::NOT_FOUND, "No link with such id");
        }

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(),
            $groupId,
            GroupPermissionLevel::WRITE)) {
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

    #[HttpPut]
    #[Route("linkGroup/{groupId}/link/{linkId}")]
    public function deleteLink(string $groupId, string $linkId): void
    {
        $link = $this->findLink($groupId, $linkId);

        if (!$link) {
            $this->response(HttpStatusCode::NOT_FOUND, "No link with such id");
        }

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(),
            $link->link_group_id,
            GroupPermissionLevel::WRITE)) {
            $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to delete this link");
        }

        $this->linkRepo->delete($linkId);
        $this->response(HttpStatusCode::OK);
    }

    #[HttpGet]
    #[Route("linkGroups")]
    public function getAllLinkGroups(): void
    {
        $linkGroups = $this->linkGroupRepo->findAllUserGroups($this->sessionHandler->getUserId());
        $this->response(HttpStatusCode::OK, $linkGroups);
    }

    #[HttpGet]
    #[Route("sharedLinkGroups")]
    public function getAllSharedLinkGroups(): void
    {
        $linkGroups = $this->linkGroupRepo->findAllUserSharedGroups($this->sessionHandler->getUserId());
        $this->response(HttpStatusCode::OK, $linkGroups);
    }

    #[HttpGet]
    #[Route("linkGroup/{groupId}")]
    public function getLinkGroup(string $groupId): void
    {
        $linkGroup = $this->linkGroupRepo->findById($groupId);

        if (!$linkGroup) {
            $this->response(HttpStatusCode::NOT_FOUND, "No link group with such id");
        }

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(),
            $groupId,
            GroupPermissionLevel::READ)) {
            $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to access this group");
        }

        $this->response(HttpStatusCode::OK, $linkGroup);
    }

    #[HttpPost]
    #[Route("linkGroup")]
    public function addLinkGroup(): void
    {
        $linkGroupData = $this->getRequestBody();
        if ($linkGroupData === null || !array_key_exists('name', $linkGroupData)) {
            $this->response(HttpStatusCode::BAD_REQUEST, "Invalid request body");
        }

        $linkGroup = new LinkGroup($this->sessionHandler->getUserId(), $linkGroupData['name']);
        $this->response(HttpStatusCode::CREATED, $this->linkGroupRepo->insert($linkGroup));
    }

    #[HttpPut]
    #[Route("linkGroup/{groupId}")]
    public function updateLinkGroup(string $groupId): void
    {
        $linkGroup = $this->linkGroupRepo->findById($groupId);

        if (!$linkGroup) {
            $this->response(HttpStatusCode::NOT_FOUND, "No link group with such id");
        }

        if (!$this->checkGroupAccess(
            $this->sessionHandler->getUserId(),
            $groupId,
            GroupPermissionLevel::WRITE)) {
            $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to edit this link group");
        }

        // Update link group data based on request body
        $linkGroupData = $this->getRequestBody();
        if ($linkGroupData === null) {
            $this->response(HttpStatusCode::BAD_REQUEST, "Invalid request body");
        }

        $linkGroup->name = $linkGroupData['name'] ?? $linkGroup->name;

        $this->response(HttpStatusCode::OK, $this->linkGroupRepo->update($linkGroup));
    }

    #[HttpDelete]
    #[Route("linkGroup/{groupId}")]
    public function deleteLinkGroup(string $groupId): void
    {
        $linkGroup = $this->linkGroupRepo->findById($groupId);

        if (!$linkGroup) {
            $this->response(HttpStatusCode::NOT_FOUND, "No link group with such id");
        }

        if ($linkGroup->user_id !== $this->sessionHandler->getUserId()) {
            $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to delete this link group");
        }

        // Delete the link group
        $this->linkGroupRepo->delete($groupId);
        $this->response(HttpStatusCode::OK);
    }

    #[HttpPost]
    #[Route("linkGroup/{groupId}/share")]
    public function addGroupShare(string $groupId): void
    {
        $group = $this->linkGroupRepo->findById($groupId);

        if (!$group) {
            $this->response(HttpStatusCode::NOT_FOUND, "No group with such id");
        }

        $shareData = $this->getRequestBody();

        if ($shareData === null ||
            !array_key_exists('user_id', $shareData) ||
            !array_key_exists('permission', $shareData) ||
            !GroupPermissionLevel::tryFrom($shareData['permission'])
        ) {
            $this->response(HttpStatusCode::BAD_REQUEST, "Invalid request body");
        }

        $shareToUserId = $shareData['user_id'];
        $linkGroupId = $groupId;
        $permissionLevel = GroupPermissionLevel::from($shareData['permission']);

        // Check if group is already shared to target user
        if ($this->checkGroupAccess($shareToUserId, $groupId, $permissionLevel)) {
            $this->response(HttpStatusCode::BAD_REQUEST, "Group is already shared to this user");
        }

        // Check if the user has access to the link group
        if (!$group->user_id !== $this->sessionHandler->getUserId()) {
            $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to share this group.");
        }

        $share = new LinkGroupShare($shareToUserId, $linkGroupId, new DateTime(), $permissionLevel);
        $this->response(HttpStatusCode::CREATED, $this->linkGroupShareRepo->insert($share));
    }

    #[HttpPut]
    #[Route("linkGroup/{groupId}/share/{shareId}")]
    public function updateGroupShare(string $groupId, string $shareId): void
    {
        $group = $this->linkGroupRepo->findById($groupId);

        if (!$group) {
            $this->response(HttpStatusCode::NOT_FOUND, "No group with such id");
        }

        $share = $this->findGroupShare($groupId, $shareId);

        if (!$share) {
            $this->response(HttpStatusCode::NOT_FOUND, "No group share with such id");
        }

        $shareData = $this->getRequestBody();

        if ($shareData === null ||
            !array_key_exists('permission', $shareData) ||
            !GroupPermissionLevel::tryFrom($shareData['permission'])
        ) {
            $this->response(HttpStatusCode::BAD_REQUEST, "Invalid request body");
        }

        if ($group->user_id !== $this->sessionHandler->getUserId()) {
            $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to edit this share");
        }

        $permissionLevel = GroupPermissionLevel::from($shareData['permission']);
        $share->permission = $permissionLevel;

        $this->response(HttpStatusCode::CREATED, $this->linkGroupShareRepo->update($share));
    }

    #[HttpDelete]
    #[Route("linkGroup/{groupId}/shares/{shareId}")]
    public function deleteGroupShare(string $groupId, string $shareId): void
    {
        $group = $this->linkGroupRepo->findById($groupId);

        if (!$group) {
            $this->response(HttpStatusCode::NOT_FOUND, "No group with such id");
        }
        
        $share = $this->findGroupShare($groupId, $shareId);

        if (!$share) {
            $this->response(HttpStatusCode::NOT_FOUND, "No group share with such id");
        }

        if ($group->user_id !== $this->sessionHandler->getUserId()) {
            $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to delete this share");
        }
        
        $this->linkGroupRepo->delete($shareId);
        $this->response(HttpStatusCode::OK);
    }


    private function findLink($groupId, $linkId): ?Link
    {
        $link = $this->linkRepo->findById($linkId);

        if (!$link) {
            return null;
        }

        if ($link->group_id !== $groupId) {
            return null;
        }

        return $link instanceof Link ? $link : null;
    }

    private function findGroupShare($groupId, $shareId): ?LinkGroupShare
    {
        $share = $this->linkGroupShareRepo->findById($shareId);

        if (!$share) {
            return null;
        }

        if ($share->group_id !== $groupId) {
            return null;
        }

        return $share instanceof LinkGroupShare ? $share : null;
    }

    private function checkGroupAccess(string $userId, string $linkGroupId,
        GroupPermissionLevel $permissionLevel): bool
    {
        $linkGroup = $this->linkGroupRepo->findById($linkGroupId);

        if ($linkGroup->user_id === $userId) {
            return true;
        }

        foreach ($linkGroup->groupShares as $share) {
            if ($share->user_id !== $userId) {
                continue;
            }

            if ($permissionLevel === GroupPermissionLevel::READ || $share->permission === $permissionLevel) {
                return true;
            }

        }

        return false;
    }
}
