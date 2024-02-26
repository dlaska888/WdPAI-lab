<?php

namespace src\Models\Entities;

use DateTime;
use src\Helpers\UUIDGenerator;
use src\Hydrators\Attributes\HydrationStrategy;
use src\Hydrators\Strategies\BooleanStrategy;
use src\Hydrators\Strategies\DateTimeStrategy;

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