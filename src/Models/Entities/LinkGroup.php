<?php

namespace LinkyApp\Models\Entities;

use LinkyApp\Hydrators\Attributes\SkipHydration;

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
