<?php

namespace src\Models\Entities;

use src\Enums\UserRole;
use src\Hydrators\Attributes\HydrationStrategy;
use src\Hydrators\Strategies\BooleanStrategy;
use src\Hydrators\Strategies\UserRoleStrategy;

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
