<?php

namespace src\Controllers;

use DateTime;
use src\attributes\authorization\Authorize;
use src\attributes\controller\ApiController;
use src\Attributes\httpMethod\HttpDelete;
use src\Attributes\httpMethod\HttpGet;
use src\Attributes\httpMethod\HttpPost;
use src\Attributes\httpMethod\HttpPut;
use src\Attributes\Route;
use src\Enums\GroupPermissionLevel;
use src\Enums\HttpStatusCode;
use src\Enums\UserRole;
use src\Handlers\UserSessionHandler;
use src\Models\Entities\Link;
use src\Models\Entities\LinkGroup;
use src\Models\Entities\LinkGroupShare;
use src\Repos\LinkGroupRepo;
use src\Repos\LinkGroupShareRepo;
use src\Repos\LinkRepo;
use src\Repos\UserRepo;
use src\Validators\AddLinkGroupShareValidator;
use src\Validators\AddLinkGroupValidator;
use src\Validators\AddLinkValidator;
use src\Validators\UpdateLinkGroupShareValidator;
use src\Validators\UpdateLinkGroupValidator;
use src\Validators\UpdateLinkValidator;

#[ApiController]
#[Authorize(UserRole::NORMAL)]
class LinkController extends AppController
{
    private LinkRepo $linkRepo;
    private LinkGroupRepo $linkGroupRepo;
    private LinkGroupShareRepo $linkGroupShareRepo;
    private UserSessionHandler $sessionHandler;
    private UserRepo $userRepo;

    public function __construct()
    {
        parent::__construct();
        $this->userRepo = new UserRepo();
        $this->linkRepo = new LinkRepo();
        $this->linkGroupRepo = new LinkGroupRepo();
        $this->linkGroupShareRepo = new LinkGroupShareRepo();
        $this->sessionHandler = new UserSessionHandler();
    }

    #[HttpGet]
    #[Route("link-group/{groupId}/link/{linkId}")]
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
    #[Route("link-group/{groupId}/link")]
    public function addLink(string $groupId): void
    {
        $linkData = $this->getRequestBody();
        $this->validationResponse($linkData, AddLinkValidator::class);

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(),
            $groupId,
            GroupPermissionLevel::WRITE)) {
            $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to create link in this group");
        }

        $link = new Link($groupId, $linkData['title'], $linkData['url']);
        $this->response(HttpStatusCode::CREATED, $this->linkRepo->insert($link));
    }

    #[HttpPut]
    #[Route("link-group/{groupId}/link/{linkId}")]
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
        $this->validationResponse($linkData, UpdateLinkValidator::class);

        $link->title = $linkData['title'] ?? $link->title;
        $link->url = $linkData['url'] ?? $link->title;
        $this->response(HttpStatusCode::OK, $this->linkRepo->update($link));
    }

    #[HttpPut]
    #[Route("link-group/{groupId}/link/{linkId}")]
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

        $this->response(HttpStatusCode::OK, $this->linkRepo->delete($linkId));
    }

    #[HttpGet]
    #[Route("link-groups")]
    public function getAllLinkGroups(): void
    {
        $linkGroups = $this->linkGroupRepo->findAllUserGroups($this->sessionHandler->getUserId());
        $this->response(HttpStatusCode::OK, $linkGroups);
    }

    #[HttpGet]
    #[Route("link-groups/shared")]
    public function getAllSharedLinkGroups(): void
    {
        $linkGroups = $this->linkGroupRepo->findAllUserSharedGroups($this->sessionHandler->getUserId());
        $this->response(HttpStatusCode::OK, $linkGroups);
    }

    #[HttpGet]
    #[Route("link-group/{groupId}")]
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
    #[Route("link-group")]
    public function addLinkGroup(): void
    {
        $linkGroupData = $this->getRequestBody();
        $this->validationResponse($linkGroupData, AddLinkGroupValidator::class);

        $linkGroup = new LinkGroup($this->sessionHandler->getUserId(), $linkGroupData['name']);
        $this->response(HttpStatusCode::CREATED, $this->linkGroupRepo->insert($linkGroup));
    }

    #[HttpPut]
    #[Route("link-group/{groupId}")]
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
        $this->validationResponse($linkGroupData, UpdateLinkGroupValidator::class);

        $linkGroup->name = $linkGroupData['name'] ?? $linkGroup->name;
        $this->response(HttpStatusCode::OK, $this->linkGroupRepo->update($linkGroup));
    }

    #[HttpDelete]
    #[Route("link-group/{groupId}")]
    public function deleteLinkGroup(string $groupId): void
    {
        $linkGroup = $this->linkGroupRepo->findById($groupId);

        if (!$linkGroup) {
            $this->response(HttpStatusCode::NOT_FOUND, "No link group with such id");
        }

        if ($linkGroup->user_id !== $this->sessionHandler->getUserId()) {
            $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to delete this link group");
        }

        $this->response(HttpStatusCode::OK, $this->linkGroupRepo->delete($groupId));
    }

    #[HttpPost]
    #[Route("link-group/{groupId}/share")]
    public function addGroupShare(string $groupId): void
    {
        // Find group
        $group = $this->linkGroupRepo->findById($groupId);

        if (!$group) {
            $this->response(HttpStatusCode::NOT_FOUND, "No group with such id");
        }

        // Check access
        if ($group->user_id !== $this->sessionHandler->getUserId()) {
            $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to share this group.");
        }

        // Validate body
        $shareData = $this->getRequestBody();
        $this->validationResponse($shareData, AddLinkGroupShareValidator::class);

        // Find user by email
        $shareToUser = $this->userRepo->findByEmail($shareData['email']);

        if ($shareToUser === null)
            $this->response(HttpStatusCode::BAD_REQUEST, "User with this email not found");

        // Check if group is already shared to target user
        $permissionLevel = GroupPermissionLevel::from($shareData['permission']);

        if ($this->sessionHandler->getUserId() === $shareToUser->user_id ||
            $this->checkGroupAccess($shareToUser->user_id, $groupId, $permissionLevel)) {
            $this->response(HttpStatusCode::BAD_REQUEST, "Group is already shared to this user");
        }

        $share = new LinkGroupShare($shareToUser->user_id, $groupId, new DateTime(), $permissionLevel);
        $this->response(HttpStatusCode::CREATED, $this->linkGroupShareRepo->insert($share));
    }

    #[HttpPut]
    #[Route("link-group/{groupId}/share/{shareId}")]
    public function updateGroupShare(string $groupId, string $shareId): void
    {
        // Find group from share
        $group = $this->linkGroupRepo->findById($groupId);

        if (!$group) {
            $this->response(HttpStatusCode::NOT_FOUND, "No group with such id");
        }

        // Check access
        if ($group->user_id !== $this->sessionHandler->getUserId()) {
            $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to edit this share");
        }

        // Find share 
        $share = $this->findGroupShare($groupId, $shareId);

        if (!$share) {
            $this->response(HttpStatusCode::NOT_FOUND, "No group share with such id");
        }

        // Find user from share
        $shareToUser = $this->userRepo->findById($share->user_id);

        if ($shareToUser === null)
            $this->response(HttpStatusCode::BAD_REQUEST, "User with this id not found");

        // Validate and update
        $shareData = $this->getRequestBody();
        $this->validationResponse($shareData, UpdateLinkGroupShareValidator::class);

        $share->permission = GroupPermissionLevel::from($shareData['permission']) ?? $share->permission;
        $this->response(HttpStatusCode::CREATED, $this->linkGroupShareRepo->update($share));
    }

    #[HttpDelete]
    #[Route("link-group/{groupId}/shares/{shareId}")]
    public function deleteGroupShare(string $groupId, string $shareId): void
    {
        $group = $this->linkGroupRepo->findById($groupId);

        if (!$group) {
            $this->response(HttpStatusCode::NOT_FOUND, "No group with such id");
        }

        if ($group->user_id !== $this->sessionHandler->getUserId()) {
            $this->response(HttpStatusCode::UNAUTHORIZED, "User is not authorized to delete this share");
        }

        $share = $this->findGroupShare($groupId, $shareId);

        if (!$share) {
            $this->response(HttpStatusCode::NOT_FOUND, "No group share with such id");
        }

        $this->response(HttpStatusCode::OK, $this->linkGroupRepo->delete($shareId));
    }

    private function findLink($groupId, $linkId): ?Link
    {
        $link = $this->linkRepo->findById($linkId);

        if (!$link) {
            return null;
        }

        if ($link->link_group_id !== $groupId) {
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

        if ($share->link_group_id !== $groupId) {
            return null;
        }

        return $share instanceof LinkGroupShare ? $share : null;
    }

    private function checkGroupAccess(string $userId, string $linkGroupId, GroupPermissionLevel $permissionLevel): bool
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
