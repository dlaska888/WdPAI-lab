<?php

namespace src\Models\Entities;

use src\Enums\UserRole;
use src\hydrators\attributes\HydrationStrategy;
use src\hydrators\strategies\BooleanStrategy;
use src\hydrators\strategies\UserRoleStrategy;

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
