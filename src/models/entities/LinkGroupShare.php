<?php

namespace src\Models\Entities;

use src\Enums\GroupPermissionLevel;
use src\hydrators\attributes\HydrationStrategy;
use src\hydrators\strategies\GroupPermissionLevelStrategy;
use src\hydrators\strategies\UserRoleStrategy;

class LinkGroupShare extends Entity
{
    public string $userId;
    
    public string $linkGroupId;
    
    #[HydrationStrategy(GroupPermissionLevelStrategy::class)]
    public GroupPermissionLevel $permission = GroupPermissionLevel::READ;
}
