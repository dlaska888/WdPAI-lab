<?php

namespace LinkyApp\Repos;

use LinkyApp\Models\Entities\LinkGroupShare;
use PDO;

class LinkGroupShareRepo extends BaseRepo
{
    protected function getEntityName(): string
    {
        return LinkGroupShare::class;
    }

    public function findLinkGroupShares(string $linkGroupId): array
    {
        $linkGroupShares = array();

        $stmt = $this->db->connect()
            ->prepare('SELECT * FROM link_group_share WHERE link_group_id = :link_group_id ORDER BY date_created');
        $stmt->execute(['link_group_id' => $linkGroupId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            $linkGroupShares[] = $this->mapToObject($result);
        }

        return $linkGroupShares;
    }

    public function findUserGroupShares(string $userId): array
    {
        $shares = array();

        $stmt = $this->db->connect()
            ->prepare('SELECT * FROM link_group_share WHERE user_id = :user_id ORDER BY date_created');
        $stmt->execute(['user_id' => $userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $result) {
            $objectResult = $this->mapToObject($result);
            $shares[] = $objectResult;
        }

        return $shares;
    }

}
