<?php

namespace src\Models\Entities;

class LinkGroup extends Entity
{
    public string $userId;
    
    public string $name;

    // TODO move to DTO object
    
    public ?bool $editable;

    public ?array $links;

    public ?array $groupShares;
}
