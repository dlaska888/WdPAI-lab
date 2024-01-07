<?php

namespace src\Controllers;

use DateTime;
use src\Enums\GroupPermissionLevel;
use src\Enums\UserRole;
use src\exceptions\BadRequestException;
use src\exceptions\NotFoundException;
use src\exceptions\UnauthorizedException;
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

        $link = new Link($groupId, $linkData['title'], $linkData['url']);
        return new Json($this->linkRepo->insert($link), HttpStatusCode::CREATED);
    }

    #[HttpPut]
    #[Route("link-group/{groupId}/link/{linkId}")]
    public function updateLink(string $groupId, string $linkId): Json
    {
        $link = $this->findLink($groupId, $linkId);

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(), $groupId, GroupPermissionLevel::WRITE)) {
            throw new UnauthorizedException("User is not authorized to edit this link");
        }

        $linkData = $this->getRequestBody();
        $this->validateRequestData($linkData, UpdateLinkValidator::class);

        $link->title = $linkData['title'] ?? $link->title;
        $link->url = $linkData['url'] ?? $link->title;

        return new Json($this->linkRepo->update($link), HttpStatusCode::OK);
    }

    #[HttpDelete]
    #[Route("link-group/{groupId}/link/{linkId}")]
    public function deleteLink(string $groupId, string $linkId): Json
    {
        $link = $this->findLink($groupId, $linkId);

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(), $link->link_group_id, GroupPermissionLevel::WRITE)) {
            throw new UnauthorizedException("User is not authorized to delete this link");
        }

        return new Json($this->linkRepo->delete($linkId), HttpStatusCode::OK);
    }

    #[HttpGet]
    #[Route("link-groups")]
    public function getAllLinkGroups(): Json
    {
        $linkGroups = $this->linkGroupRepo->findAllUserGroups($this->sessionHandler->getUserId());
        
        foreach ($linkGroups as &$linkGroup){
            $linkGroup = $this->mapEditable($linkGroup);
        }
        
        return new Json($linkGroups, HttpStatusCode::OK);
    }

    #[HttpGet]
    #[Route("link-groups/shared")]
    public function getAllSharedLinkGroups(): Json
    {
        $linkGroups = $this->linkGroupRepo->findAllUserSharedGroups($this->sessionHandler->getUserId());
        
        foreach ($linkGroups as &$linkGroup){
            $linkGroup = $this->mapEditable($linkGroup);
        }
        
        return new Json($linkGroups, HttpStatusCode::OK);
    }

    #[HttpGet]
    #[Route("link-group/{groupId}")]
    public function getLinkGroup(string $groupId): Json
    {
        $linkGroup = $this->linkGroupRepo->findById($groupId);

        if (!$this->checkGroupAccess($this->sessionHandler->getUserId(), $groupId, GroupPermissionLevel::READ)) {
            throw new UnauthorizedException("User is not authorized to access this group");
        }
        
        $linkGroup = $this->mapEditable($linkGroup);
        return new Json($linkGroup, HttpStatusCode::OK);
    }

    #[HttpPost]
    #[Route("link-group")]
    public function addLinkGroup(): Json
    {
        $linkGroupData = $this->getRequestBody();
        $this->validateRequestData($linkGroupData, AddLinkGroupValidator::class);

        $linkGroup = new LinkGroup($this->sessionHandler->getUserId(), $linkGroupData['name']);
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
        
        $linkGroup = $this->mapEditable($linkGroup);
        return new Json($linkGroup, HttpStatusCode::OK);
    }

    #[HttpDelete]
    #[Route("link-group/{groupId}")]
    public function deleteLinkGroup(string $groupId): Json
    {
        $linkGroup = $this->linkGroupRepo->findById($groupId);

        if ($linkGroup->user_id !== $this->sessionHandler->getUserId()) {
            throw new UnauthorizedException("User is not authorized to delete this link group");
        }

        return new Json($this->linkGroupRepo->delete($groupId), HttpStatusCode::OK);
    }

    #[HttpPost]
    #[Route("link-group/{groupId}/share")]
    public function addGroupShare(string $groupId): Json
    {
        $group = $this->linkGroupRepo->findById($groupId);

        if ($group->user_id !== $this->sessionHandler->getUserId()) {
            throw new UnauthorizedException("User is not authorized to share this group");
        }

        $shareData = $this->getRequestBody();
        $this->validateRequestData($shareData, AddLinkGroupShareValidator::class);

        $shareToUser = $this->userRepo->findByEmail($shareData['email']);

        $permissionLevel = GroupPermissionLevel::from($shareData['permission']);

        if ($this->sessionHandler->getUserId() === $shareToUser->user_id ||
            $this->checkGroupAccess($shareToUser->user_id, $groupId, $permissionLevel)) {
            throw new BadRequestException("Group is already shared to this user");
        }

        $share = new LinkGroupShare($shareToUser->user_id, $groupId, new DateTime(), $permissionLevel);
        return new Json($this->linkGroupShareRepo->insert($share), HttpStatusCode::CREATED);
    }

    #[HttpPut]
    #[Route("link-group/{groupId}/share/{shareId}")]
    public function updateGroupShare(string $groupId, string $shareId): Json
    {
        $group = $this->linkGroupRepo->findById($groupId);

        if ($group->user_id !== $this->sessionHandler->getUserId()) {
            throw new UnauthorizedException("User is not authorized to edit this share");
        }

        $share = $this->findGroupShare($groupId, $shareId);
        $this->userRepo->findById($share->user_id); // check if user exists

        $shareData = $this->getRequestBody();
        $this->validateRequestData($shareData, UpdateLinkGroupShareValidator::class);

        $share->permission = GroupPermissionLevel::from($shareData['permission']) ?? $share->permission;
        return new Json($this->linkGroupShareRepo->update($share), HttpStatusCode::CREATED);
    }

    #[HttpDelete]
    #[Route("link-group/{groupId}/shares/{shareId}")]
    public function deleteGroupShare(string $groupId, string $shareId): Json
    {
        $group = $this->linkGroupRepo->findById($groupId);

        if ($group->user_id !== $this->sessionHandler->getUserId()) {
            throw new UnauthorizedException("User is not authorized to delete this share");
        }

        $this->findGroupShare($groupId, $shareId); // check if share exists

        return new Json($this->linkGroupRepo->delete($shareId), HttpStatusCode::OK);
    }

    private function findLink($groupId, $linkId): ?Link
    {
        $link = $this->linkRepo->findById($linkId);

        if ($link->link_group_id !== $groupId) {
            throw new BadRequestException("No link with such id in this group");
        }

        return $link;
    }

    private function findGroupShare($groupId, $shareId): ?LinkGroupShare
    {
        $share = $this->linkGroupShareRepo->findById($shareId);

        if ($share->link_group_id !== $groupId) {
            throw new BadRequestException("No share with such id for this group");
        }

        return $share;
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
    
    //TODO refactor to some kind of mapper
    private function mapEditable(LinkGroup $linkGroup) : LinkGroup
    {
        if($this->checkGroupAccess($this->sessionHandler->getUserId(), $linkGroup->link_group_id, GroupPermissionLevel::WRITE))
            $linkGroup->editable = true;
        else
            $linkGroup->editable = false;
        
        return $linkGroup;
    }
}
