<?php

namespace src\Repos;

use DateTime;
use src\Models\Entities\File;

class FileRepo extends BaseRepo
{
    protected function getTableName(): string
    {
        return 'File';
    }

    protected function getIdName(): string
    {
        return 'file_id';
    }

    protected function mapToObject(array $data): File
    {
        return new File(
            name: $data['name'],
            file_id: $data['file_id'],
            date_created: new DateTime($data['date_created'])
        );
    }

    protected function mapToArray(object $entity): array
    {
        return [
            'file_id' => $entity->file_id,
            'name' => $entity->name,
            'date_created' => $entity->date_created->format('Y-m-d H:i:s'),
        ];
    }
}