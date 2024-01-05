<?php

namespace src\Controllers;

use DateTime;
use src\Enums\GroupPermissionLevel;
use src\Enums\UserRole;
use src\Handlers\UserSessionHandler;
use src\LinkyRouting\attributes\controller\ApiController;
use src\Models\Entities\Link;
use src\Models\Entities\LinkGroup;
use src\Models\Entities\LinkGroupShare;
use src\Repos\LinkGroupRepo;
use src\Repos\LinkGroupShareRepo;
use src\Repos\LinkRepo;
use src\Repos\UserRepo;
use src\LinkyRouting\attributes\authorization\Authorize;
use src\LinkyRouting\attributes\controller\Controller;
use src\LinkyRouting\attributes\httpMethod\HttpDelete;
use src\LinkyRouting\attributes\httpMethod\HttpGet;
use src\LinkyRouting\attributes\httpMethod\HttpPost;
use src\LinkyRouting\attributes\httpMethod\HttpPut;
use src\LinkyRouting\attributes\Route;
use src\LinkyRouting\enums\HttpStatusCode;
use src\LinkyRouting\Responses\Json;
use src\Validators\AddLinkGroupShareValidator;
use src\Validators\AddLinkGroupValidator;
use src\Validators\AddLinkValidator;
use src\Validators\UpdateLinkGroupShareValidator;
use src\Validators\UpdateLinkGroupValidator;
use src\Validators\UpdateLinkValidator;

#[ApiController]
#[Authorize([UserRole::NORMAL->value, UserRole::ADMIN->value])]
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
    public function getLink(string $groupId, string $linkId): Json
    {
        $link = $this->findLink($groupId, $linkId);

        if (!$link) {
            return new Json("No link with such id in this group", HttpStatusCode::NOT_FOUND);
        }

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(), $groupId, GroupPermissionLevel::READ)) {
            return new Json("User is not authorized to access this link", HttpStatusCode::UNAUTHORIZED);
        }

        return new Json($link, HttpStatusCode::OK);
    }

    #[HttpPost]
    #[Route("link-group/{groupId}/link")]
    public function addLink(string $groupId): Json
    {
        $linkData = $this->getRequestBody();
        
        $validationResult = $this->getValidationResult($linkData, AddLinkValidator::class);

        if (!$validationResult->isSuccess()) {
            return new Json($validationResult, HttpStatusCode::BAD_REQUEST);
        }

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(), $groupId, GroupPermissionLevel::WRITE)) {
            return new Json("User is not authorized to create link in this group", HttpStatusCode::UNAUTHORIZED);
        }

        $link = new Link($groupId, $linkData['title'], $linkData['url']);
        return new Json($this->linkRepo->insert($link), HttpStatusCode::CREATED);
    }

    #[HttpPut]
    #[Route("link-group/{groupId}/link/{linkId}")]
    public function updateLink(string $groupId, string $linkId): Json
    {
        $link = $this->findLink($groupId, $linkId);

        if (!$link) {
            return new Json("No link with such id", HttpStatusCode::NOT_FOUND);
        }

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(), $groupId, GroupPermissionLevel::WRITE)) {
            return new Json("User is not authorized to edit this link", HttpStatusCode::UNAUTHORIZED);
        }

        $linkData = $this->getRequestBody();
        $validationResult = $this->getValidationResult($linkData, UpdateLinkValidator::class);

        if (!$validationResult->isSuccess()) {
            return new Json($validationResult, HttpStatusCode::BAD_REQUEST);
        }

        $link->title = $linkData['title'] ?? $link->title;
        $link->url = $linkData['url'] ?? $link->title;

        return new Json($this->linkRepo->update($link), HttpStatusCode::OK);
    }

    #[HttpDelete]
    #[Route("link-group/{groupId}/link/{linkId}")]
    public function deleteLink(string $groupId, string $linkId): Json
    {
        $link = $this->findLink($groupId, $linkId);

        if (!$link) {
            return new Json("No link with such id", HttpStatusCode::NOT_FOUND);
        }

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(), $link->link_group_id, GroupPermissionLevel::WRITE)) {
            return new Json("User is not authorized to delete this link", HttpStatusCode::UNAUTHORIZED);
        }

        return new Json($this->linkRepo->delete($linkId), HttpStatusCode::OK);
    }

    #[HttpGet]
    #[Route("link-groups")]
    public function getAllLinkGroups(): Json
    {
        $linkGroups = $this->linkGroupRepo->findAllUserGroups($this->sessionHandler->getUserId());
        return new Json($linkGroups, HttpStatusCode::OK);
    }

    #[HttpGet]
    #[Route("link-groups/shared")]
    public function getAllSharedLinkGroups(): Json
    {
        $linkGroups = $this->linkGroupRepo->findAllUserSharedGroups($this->sessionHandler->getUserId());
        return new Json($linkGroups, HttpStatusCode::OK);
    }

    #[HttpGet]
    #[Route("link-group/{groupId}")]
    public function getLinkGroup(string $groupId): Json
    {
        $linkGroup = $this->linkGroupRepo->findById($groupId);

        if (!$linkGroup) {
            return new Json("No link group with such id", HttpStatusCode::NOT_FOUND);
        }

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(), $groupId, GroupPermissionLevel::READ)) {
            return new Json("User is not authorized to access this group", HttpStatusCode::UNAUTHORIZED);
        }

        return new Json($linkGroup, HttpStatusCode::OK);
    }

    #[HttpPost]
    #[Route("link-group")]
    public function addLinkGroup(): Json
    {
        $linkGroupData = $this->getRequestBody();
        $validationResult = $this->getValidationResult($linkGroupData, AddLinkGroupValidator::class);

        if (!$validationResult->isSuccess()) {
            return new Json($validationResult, HttpStatusCode::BAD_REQUEST);
        }

        $linkGroup = new LinkGroup($this->sessionHandler->getUserId(), $linkGroupData['name']);
        return new Json($this->linkGroupRepo->insert($linkGroup), HttpStatusCode::CREATED);
    }

    #[HttpPut]
    #[Route("link-group/{groupId}")]
    public function updateLinkGroup(string $groupId): Json
    {
        $linkGroup = $this->linkGroupRepo->findById($groupId);

        if (!$linkGroup) {
            return new Json("No link group with such id", HttpStatusCode::NOT_FOUND);
        }

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(), $groupId, GroupPermissionLevel::WRITE)) {
            return new Json("User is not authorized to edit this link group", HttpStatusCode::UNAUTHORIZED);
        }

        // Update link group data based on request body
        $linkGroupData = $this->getRequestBody();
        $validationResult = $this->getValidationResult($linkGroupData, UpdateLinkGroupValidator::class);

        if (!$validationResult->isSuccess()) {
            return new Json($validationResult, HttpStatusCode::BAD_REQUEST);
        }

        $linkGroup->name = $linkGroupData['name'] ?? $linkGroup->name;

        return new Json($this->linkGroupRepo->update($linkGroup), HttpStatusCode::OK);
    }

    #[HttpDelete]
    #[Route("link-group/{groupId}")]
    public function deleteLinkGroup(string $groupId): Json
    {
        $linkGroup = $this->linkGroupRepo->findById($groupId);

        if (!$linkGroup) {
            return new Json("No link group with such id", HttpStatusCode::NOT_FOUND);
        }

        if ($linkGroup->user_id !== $this->sessionHandler->getUserId()) {
            return new Json("User is not authorized to delete this link group", HttpStatusCode::UNAUTHORIZED);
        }

        return new Json($this->linkGroupRepo->delete($groupId), HttpStatusCode::OK);
    }

    #[HttpPost]
    #[Route("link-group/{groupId}/share")]
    public function addGroupShare(string $groupId): Json
    {
        // Find group
        $group = $this->linkGroupRepo->findById($groupId);

        if (!$group) {
            return new Json("No group with such id", HttpStatusCode::NOT_FOUND);
        }

        // Check access
        if ($group->user_id !== $this->sessionHandler->getUserId()) {
            return new Json("User is not authorized to share this group.", HttpStatusCode::UNAUTHORIZED);
        }

        // Validate body
        $shareData = $this->getRequestBody();
        $validationResult = $this->getValidationResult($shareData, AddLinkGroupShareValidator::class);

        if (!$validationResult->isSuccess()) {
            return new Json($validationResult, HttpStatusCode::BAD_REQUEST);
        }

        // Find user by email
        $shareToUser = $this->userRepo->findByEmail($shareData['email']);

        if ($shareToUser === null) {
            return new Json("User with this email not found", HttpStatusCode::BAD_REQUEST);
        }

        // Check if group is already shared to target user
        $permissionLevel = GroupPermissionLevel::from($shareData['permission']);

        if ($this->sessionHandler->getUserId() === $shareToUser->user_id ||
            $this->checkGroupAccess($shareToUser->user_id, $groupId, $permissionLevel)) {
            return new Json("Group is already shared to this user", HttpStatusCode::BAD_REQUEST);
        }

        $share = new LinkGroupShare($shareToUser->user_id, $groupId, new DateTime(), $permissionLevel);
        return new Json($this->linkGroupShareRepo->insert($share), HttpStatusCode::CREATED);
    }

    #[HttpPut]
    #[Route("link-group/{groupId}/share/{shareId}")]
    public function updateGroupShare(string $groupId, string $shareId): Json
    {
        // Find group from share
        $group = $this->linkGroupRepo->findById($groupId);

        if (!$group) {
            return new Json("No group with such id", HttpStatusCode::NOT_FOUND);
        }

        // Check access
        if ($group->user_id !== $this->sessionHandler->getUserId()) {
            return new Json("User is not authorized to edit this share", HttpStatusCode::UNAUTHORIZED);
        }

        // Find share 
        $share = $this->findGroupShare($groupId, $shareId);

        if (!$share) {
            return new Json("No group share with such id", HttpStatusCode::NOT_FOUND);
        }

        // Find user from share
        $shareToUser = $this->userRepo->findById($share->user_id);

        if ($shareToUser === null) {
            return new Json("User with this id not found", HttpStatusCode::BAD_REQUEST);
        }

        // Validate and update
        $shareData = $this->getRequestBody();
        $validationResult = $this->getValidationResult($shareData, UpdateLinkGroupShareValidator::class);

        if (!$validationResult->isSuccess()) {
            return new Json($validationResult, HttpStatusCode::BAD_REQUEST);
        }

        $share->permission = GroupPermissionLevel::from($shareData['permission']) ?? $share->permission;
        return new Json($this->linkGroupShareRepo->update($share), HttpStatusCode::CREATED);
    }

    #[HttpDelete]
    #[Route("link-group/{groupId}/shares/{shareId}")]
    public function deleteGroupShare(string $groupId, string $shareId): Json
    {
        $group = $this->linkGroupRepo->findById($groupId);

        if (!$group) {
            return new Json("No group with such id", HttpStatusCode::NOT_FOUND);
        }

        if ($group->user_id !== $this->sessionHandler->getUserId()) {
            return new Json("User is not authorized to delete this share", HttpStatusCode::UNAUTHORIZED);
        }

        $share = $this->findGroupShare($groupId, $shareId);

        if (!$share) {
            return new Json("No group share with such id", HttpStatusCode::NOT_FOUND);
        }

        return new Json($this->linkGroupRepo->delete($shareId), HttpStatusCode::OK);
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
