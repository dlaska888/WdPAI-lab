<?php

namespace src\Repos;

use src\Models\Entities\File;

class FileRepo extends BaseRepo
{
    protected function getEntityName(): string
    {
        return File::class;
    }
}