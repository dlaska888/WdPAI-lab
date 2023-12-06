<?php

namespace src\Repos;

use src\Enums\GroupPermissionLevel;
use src\Models\LinkGroupShare;
use DateTime;

class LinkGroupShareRepo extends BaseRepo
{
    protected function getTableName(): string
    {
        return 'LinkGroupShare';
    }

    protected function getIdName(): string
    {
        return 'link_group_share_id';
    }

    protected function mapToObject(array $data): LinkGroupShare
    {
        return new LinkGroupShare(
            user_id: $data['user_id'],
            link_group_id: $data['link_group_id'],
            date_created: new DateTime($data['date_created']),
            permission: GroupPermissionLevel::from($data['permission']),
            link_group_share_id: $data['link_group_share_id']
        );
    }

    protected function mapToArray(object $entity): array
    {
        return [
            'link_group_share_id' => $entity->link_group_share_id,
            'user_id' => $entity->user_id,
            'link_group_id' => $entity->link_group_id,
            'permission' => $entity->permission->value,
            'date_created' => $entity->date_created->format('Y-m-d H:i:s'),
        ];
    }

    public function findLinkGroupShares(string $linkGroupId): array
    {
        $linkGroupShares = array();

        $stmt = $this->db->connect()->prepare('SELECT * FROM LinkGroupShare WHERE link_group_id = :link_group_id');
        $stmt->execute(['link_group_id' => $linkGroupId]);
        $results = $stmt->fetchAll();

        foreach ($results as $result) {
            $linkGroupShares[] = $this->mapToObject($result);
        }

        return $linkGroupShares;
    }

    public function findUserGroupShares(string $userId) : array
    {
        $shares = array();

        $stmt = $this->db->connect()->prepare('SELECT * FROM LinkGroupShare WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        $results = $stmt->fetchAll();

        foreach ($results as $result) {
            $objectResult = $this->mapToObject($result);
            $shares[] = $objectResult;
        }

        return $shares;
    }

}
