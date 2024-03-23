<?php

namespace LinkyApp\Models\Entities;

class Link extends Entity
{
    public string $linkGroupId;
    
    public string $title;
    
    public string $url;
    
    public float $customOrder;
}
