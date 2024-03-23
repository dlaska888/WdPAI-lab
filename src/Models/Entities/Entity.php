<?php

namespace LinkyApp\Models\Entities;

use DateTime;
use LinkyApp\Helpers\UUIDGenerator;
use LinkyApp\Hydrators\Attributes\HydrationStrategy;
use LinkyApp\Hydrators\Strategies\BooleanStrategy;
use LinkyApp\Hydrators\Strategies\DateTimeStrategy;

class Entity
{
    public string $id;
    
    #[HydrationStrategy(DateTimeStrategy::class)]
    public DateTime $dateCreated;
    
    #[HydrationStrategy(BooleanStrategy::class)]
    public bool $isDeleted = false;
    
    public function __construct()
    {
        $this->id = UUIDGenerator::v4();
        $this->dateCreated = new DateTime();
    }
}