<?php

namespace src\Models\Entities;

use src\Enums\GroupPermissionLevel;
use src\Hydrators\Attributes\HydrationStrategy;
use src\Hydrators\Strategies\GroupPermissionLevelStrategy;

class LinkGroupShare extends Entity
{
    public string $userId;
    
    public string $linkGroupId;
    
    #[HydrationStrategy(GroupPermissionLevelStrategy::class)]
    public GroupPermissionLevel $permission = GroupPermissionLevel::READ;
}
