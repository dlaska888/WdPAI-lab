<?php

namespace LinkyApp\Models\Entities;

use LinkyApp\Enums\UserRole;
use LinkyApp\Hydrators\Attributes\HydrationStrategy;
use LinkyApp\Hydrators\Strategies\BooleanStrategy;
use LinkyApp\Hydrators\Strategies\UserRoleStrategy;

class LinkyUser extends Entity
{
    public string $userName;
    
    public string $email;
    
    public string $passwordHash;
    
    #[HydrationStrategy(BooleanStrategy::class)]
    public bool $emailConfirmed = false;

    #[HydrationStrategy(UserRoleStrategy::class)]
    public UserRole $role = UserRole::NORMAL;
    
    public ?string $profilePictureId;
}
