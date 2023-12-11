<?php

namespace src\Models\Entities;

use src\Helpers\UUIDGenerator;

class File
{
    public string $file_id;
    public string $name;
    
    public function __construct(
        string $name,
        string $file_id = null
    ) {
        $this->file_id = $file_id ?? UUIDGenerator::genUUID();
        $this->name = $name;
    }
}