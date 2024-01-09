<?php

namespace src\Models\Entities;

use DateTime;
use src\Helpers\UUIDGenerator;

class File
{
    public string $file_id;
    public string $name;
    public DateTime $date_created;
    
    public function __construct(
        string $name,
        string $file_id = null,
        DateTime $date_created = new DateTime()
    ) {
        $this->file_id = $file_id ?? UUIDGenerator::genUUID();
        $this->name = $name;
        $this->date_created = $date_created;
    }
}