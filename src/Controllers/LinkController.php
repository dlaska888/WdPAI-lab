<?php

namespace src\Controllers;

use src\Enums\GroupPermissionLevel;
use src\Enums\UserRole;
use src\Exceptions\BadRequestException;
use src\Exceptions\UnauthorizedException;
use src\Handlers\UserSessionHandler;
use src\LinkyRouting\Attributes\Authorization\Authorize;
use src\LinkyRouting\Attributes\Controller\ApiController;
use src\LinkyRouting\Attributes\HttpMethod\HttpDelete;
use src\LinkyRouting\Attributes\HttpMethod\HttpGet;
use src\LinkyRouting\Attributes\HttpMethod\HttpPost;
use src\LinkyRouting\Attributes\HttpMethod\HttpPut;
use src\LinkyRouting\Attributes\Route;
use src\LinkyRouting\Enums\HttpStatusCode;
use src\LinkyRouting\Responses\Json;
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
use src\Validators\SearchLinkGroupsValidator;
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
        $link = $this->linkRepo->findWithGroupId($linkId, $groupId);

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(), $groupId, GroupPermissionLevel::READ)) {
            throw new UnauthorizedException("User is not authorized to access this link");
        }

        return new Json($link, HttpStatusCode::OK);
    }

    #[HttpPost]
    #[Route("link-group/{groupId}/link")]
    public function addLink(string $groupId): Json
    {
        $linkData = $this->getRequestBody();
        $this->validateRequestData($linkData, AddLinkValidator::class);

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(), $groupId, GroupPermissionLevel::WRITE)) {
            throw new UnauthorizedException("User is not authorized to create link in this group");
        }

        $link = new Link();
        $link->linkGroupId = $groupId;
        $link->title = $linkData['title'];
        $link->url = $linkData['url'];

        return new Json($this->linkRepo->insert($link), HttpStatusCode::CREATED);
    }

    #[HttpPut]
    #[Route("link-group/{groupId}/link/{linkId}")]
    public function updateLink(string $groupId, string $linkId): Json
    {
        $link = $this->linkRepo->findWithGroupId($linkId, $groupId);

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(), $groupId, GroupPermissionLevel::WRITE)) {
            throw new UnauthorizedException("User is not authorized to edit this link");
        }

        $linkData = $this->getRequestBody();
        $this->validateRequestData($linkData, UpdateLinkValidator::class);

        $link->title = $linkData['title'] ?? $link->title;
        $link->url = $linkData['url'] ?? $link->url;

        return new Json($this->linkRepo->update($link), HttpStatusCode::OK);
    }

    #[HttpDelete]
    #[Route("link-group/{groupId}/link/{linkId}")]
    public function deleteLink(string $groupId, string $linkId): Json
    {
        $link = $this->linkRepo->findWithGroupId($linkId, $groupId);

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(),
            $link->linkGroupId,
            GroupPermissionLevel::WRITE)) {
            throw new UnauthorizedException("User is not authorized to delete this link");
        }

        return new Json($this->linkRepo->delete($linkId), HttpStatusCode::OK);
    }

    #[HttpGet]
    #[Route("link-groups")]
    public function getAllLinkGroups(): Json
    {
        $linkGroups = $this->linkGroupRepo->findAllUserGroups($this->sessionHandler->getUserId());

        foreach ($linkGroups as &$linkGroup) {
            $linkGroup = $this->resolveEditable($linkGroup);
            $linkGroup = $this->resolveShared($linkGroup);
        }

        return new Json($linkGroups, HttpStatusCode::OK);
    }

    #[HttpGet]
    #[Route("link-groups/shared")]
    public function getAllSharedLinkGroups(): Json
    {
        $linkGroups = $this->linkGroupRepo->findAllUserSharedGroups($this->sessionHandler->getUserId());

        foreach ($linkGroups as &$linkGroup) {
            $linkGroup = $this->resolveEditable($linkGroup);
            $linkGroup = $this->resolveShared($linkGroup);
        }

        return new Json($linkGroups, HttpStatusCode::OK);
    }

    #[HttpGet]
    #[Route("link-groups/search")]
    public function getLinkGroupsByName(): Json
    {
        $this->validateRequestData($_GET, SearchLinkGroupsValidator::class);
        $name = $_GET['name'];

        $matchingLinkGroups = $this->linkGroupRepo->findLinkGroupsByName(
            $this->sessionHandler->getUserId(),
            $name
        );

        foreach ($matchingLinkGroups as &$linkGroup) {
            $linkGroup = $this->resolveEditable($linkGroup);
            $linkGroup = $this->resolveShared($linkGroup);
        }

        return new Json($matchingLinkGroups, HttpStatusCode::OK);
    }

    #[HttpGet]
    #[Route("link-groups/shared/search")]
    public function getSharedLinkGroupsByName(): Json
    {
        $this->validateRequestData($_GET, SearchLinkGroupsValidator::class);
        $name = $_GET['name'];

        $matchingLinkGroups = $this->linkGroupRepo->findSharedLinkGroupsByName(
            $this->sessionHandler->getUserId(),
            $name
        );

        foreach ($matchingLinkGroups as &$linkGroup) {
            $linkGroup = $this->resolveEditable($linkGroup);
            $linkGroup = $this->resolveShared($linkGroup);
        }

        return new Json($matchingLinkGroups, HttpStatusCode::OK);
    }

    #[HttpGet]
    #[Route("link-group/{groupId}")]
    public function getLinkGroup(string $groupId): Json
    {
        $linkGroup = $this->linkGroupRepo->findById($groupId);

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(), $groupId, GroupPermissionLevel::READ)) {
            throw new UnauthorizedException("User is not authorized to access this group");
        }

        $linkGroup = $this->resolveEditable($linkGroup);
        $linkGroup = $this->resolveShared($linkGroup);
        return new Json($linkGroup, HttpStatusCode::OK);
    }

    #[HttpPost]
    #[Route("link-group")]
    public function addLinkGroup(): Json
    {
        $linkGroupData = $this->getRequestBody();
        $this->validateRequestData($linkGroupData, AddLinkGroupValidator::class);

        $linkGroup = new LinkGroup();
        $linkGroup->userId = $this->sessionHandler->getUserId();
        $linkGroup->name = $linkGroupData['name'];

        $linkGroup = $this->linkGroupRepo->insert($linkGroup);

        $linkGroup->editable = true;
        return new Json($linkGroup, HttpStatusCode::CREATED);
    }

    #[HttpPut]
    #[Route("link-group/{groupId}")]
    public function updateLinkGroup(string $groupId): Json
    {
        $linkGroup = $this->linkGroupRepo->findById($groupId);

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(), $groupId, GroupPermissionLevel::WRITE)) {
            throw new UnauthorizedException("User is not authorized to edit this link group");
        }

        // Update link group data based on request body
        $linkGroupData = $this->getRequestBody();
        $this->validateRequestData($linkGroupData, UpdateLinkGroupValidator::class);

        $linkGroup->name = $linkGroupData['name'] ?? $linkGroup->name;
        $linkGroup = $this->linkGroupRepo->update($linkGroup);

        $linkGroup = $this->resolveEditable($linkGroup);
        $linkGroup = $this->resolveShared($linkGroup);
        return new Json($linkGroup, HttpStatusCode::OK);
    }

    #[HttpDelete]
    #[Route("link-group/{groupId}")]
    public function deleteLinkGroup(string $groupId): Json
    {
        $linkGroup = $this->linkGroupRepo->findById($groupId);

        if ($linkGroup->userId !== $this->sessionHandler->getUserId()) {
            throw new UnauthorizedException("User is not authorized to delete this link group");
        }

        return new Json($this->linkGroupRepo->delete($groupId), HttpStatusCode::OK);
    }

    #[HttpPost]
    #[Route("link-group/{groupId}/shares")]
    public function addGroupShare(string $groupId): Json
    {
        $userId = $this->sessionHandler->getUserId();

        $shareData = $this->getRequestBody();
        $this->validateRequestData($shareData, AddLinkGroupShareValidator::class);

        $shareToUser = $this->userRepo->findByEmail($shareData['email']);

        if (!$shareToUser) {
            throw new BadRequestException("User with this email not found");
        }

        $permissionLevel = GroupPermissionLevel::from($shareData['permission']);

        if (!$this->checkGroupAccess($userId, $groupId, $permissionLevel)) {
            throw new UnauthorizedException("User is not authorized to share this group");
        }

        if ($this->checkGroupAccess($shareToUser->id, $groupId, $permissionLevel) ||
            $userId === $shareToUser->id) {
            throw new BadRequestException("Group is already shared to this user");
        }

        $share = new LinkGroupShare();
        $share->userId = $shareToUser->id;
        $share->linkGroupId = $groupId;
        $share->permission = $permissionLevel;

        return new Json($this->linkGroupShareRepo->insert($share), HttpStatusCode::CREATED);
    }

    #[HttpPut]
    #[Route("link-group/{groupId}/shares/{shareId}")]
    public function updateGroupShare(string $groupId, string $shareId): Json
    {
        $userId = $this->sessionHandler->getUserId();
        $share = $this->findGroupShare($groupId, $shareId);

        if (!$this->checkGroupAccess($userId, $groupId, GroupPermissionLevel::WRITE) ||
            $share->userId === $userId) {
            throw new UnauthorizedException("User is not authorized to edit this share");
        }

        $shareData = $this->getRequestBody();
        $this->validateRequestData($shareData, UpdateLinkGroupShareValidator::class);

        $share->permission = GroupPermissionLevel::from($shareData['permission']) ?? $share->permission;
        return new Json($this->linkGroupShareRepo->update($share), HttpStatusCode::CREATED);
    }

    #[HttpDelete]
    #[Route("link-group/{groupId}/shares/{shareId}")]
    public function deleteGroupShare(string $groupId, string $shareId): Json
    {
        $userId = $this->sessionHandler->getUserId();
        $share = $this->findGroupShare($groupId, $shareId); // check if share exists

        if (!$this->checkGroupAccess($userId, $groupId, GroupPermissionLevel::WRITE) ||
            $share->userId === $userId) {
            throw new UnauthorizedException("User is not authorized to delete this share");
        }

        return new Json($this->linkGroupShareRepo->delete($shareId), HttpStatusCode::OK);
    }

    private function findGroupShare($groupId, $shareId): ?LinkGroupShare
    {
        $share = $this->linkGroupShareRepo->findById($shareId);

        if ($share->linkGroupId !== $groupId) {
            throw new BadRequestException("No share with such id for this group");
        }

        return $share;
    }

    private function checkGroupAccess(string $userId, string $linkGroupId, GroupPermissionLevel $permissionLevel): bool
    {
        $linkGroup = $this->linkGroupRepo->findById($linkGroupId);

        if ($linkGroup->userId === $userId) {
            return true;
        }

        foreach ($linkGroup->groupShares as $share) {
            if ($share->userId !== $userId) {
                continue;
            }

            if ($permissionLevel === GroupPermissionLevel::READ || $share->permission === $permissionLevel) {
                return true;
            }
        }

        return false;
    }

    //TODO refactor to some kind of mapper
    private function resolveEditable(LinkGroup $linkGroup): LinkGroup
    {
        $userId = $this->sessionHandler->getUserId();

        $linkGroup->editable = $this->checkGroupAccess(
            $userId,
            $linkGroup->id,
            GroupPermissionLevel::WRITE
        );

        if ($linkGroup->editable) {
            // remove self share
            $linkGroup->groupShares = array_values(array_filter($linkGroup->groupShares,
                fn($share) => $share->userId !== $userId));
        } else {
            // cannot view group shares with read-only permissions
            $linkGroup->groupShares = [];
        }

        return $linkGroup;
    }

    private function resolveShared(LinkGroup $linkGroup): LinkGroup
    {
        $linkGroup->shared = $this->sessionHandler->getUserId() !== $linkGroup->userId;
        return $linkGroup;
    }
}
