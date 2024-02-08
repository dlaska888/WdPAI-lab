<?php

namespace src\Repos;

use PDO;
use src\Exceptions\NotFoundException;
use src\Models\Entities\Link;

class LinkRepo extends BaseRepo
{
    protected function getEntityName(): string
    {
        return Link::class;
    }

    public function findWithGroupId($linkId, $groupId)
    {
        $sql = "SELECT * FROM {$this->getTableName()} WHERE id = :id AND link_group_id = :link_group_id";

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute(['id' => $linkId, 'link_group_id' => $groupId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new NotFoundException("Link with this id not found in this group");
        }

        return $this->mapToObject($result);
    }

    public function findGroupLinks($groupId): array
    {
        $linkGroups = [];

        $stmt = $this->db->connect()
            ->prepare('SELECT * FROM link WHERE link_group_id = :link_group_id ORDER BY date_created');
        $stmt->execute(['link_group_id' => $groupId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            $linkGroups[] = $this->mapToObject($result);
        }

        return $linkGroups;
    }
}
