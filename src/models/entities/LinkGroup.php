<?php

namespace src\Models\Entities;

use src\hydrators\attributes\SkipHydration;

class LinkGroup extends Entity
{
    public string $userId;
    
    public string $name;

    #[SkipHydration]
    public ?bool $shared;

    #[SkipHydration]
    public ?bool $editable;

    #[SkipHydration]
    public ?array $links;

    #[SkipHydration]
    public ?array $groupShares;
}
