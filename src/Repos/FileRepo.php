<?php

namespace LinkyApp\Repos;

use LinkyApp\Models\Entities\File;

class FileRepo extends BaseRepo
{
    protected function getEntityName(): string
    {
        return File::class;
    }
}