<?php

namespace src\Models\Entities;

use DateTime;
use src\Helpers\UUIDGenerator;
use src\hydrators\attributes\HydrationStrategy;
use src\hydrators\strategies\BooleanStrategy;
use src\hydrators\strategies\DateTimeStrategy;

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