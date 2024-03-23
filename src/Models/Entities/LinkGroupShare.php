<?php

namespace LinkyApp\Models\Entities;

use LinkyApp\Enums\GroupPermissionLevel;
use LinkyApp\Hydrators\Attributes\HydrationStrategy;
use LinkyApp\Hydrators\Strategies\GroupPermissionLevelStrategy;

class LinkGroupShare extends Entity
{
    public string $userId;
    
    public string $linkGroupId;
    
    #[HydrationStrategy(GroupPermissionLevelStrategy::class)]
    public GroupPermissionLevel $permission = GroupPermissionLevel::READ;
}
