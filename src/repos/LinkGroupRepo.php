<?php

require_once "src/repos/BaseRepo.php";
require_once "src/models/LinkGroup.php";

class LinkGroupRepo extends BaseRepo
{
    protected function getTableName(): string
    {
        return 'LinkGroup';
    }

    protected function getIdName(): string
    {
        return 'link_group_id';
    }

    protected function mapToObject(array $data): LinkGroup
    {
        return new LinkGroup(
            user_id: $data['user_id'],
            name: $data['name'],
            date_created: new DateTime($data['date_created']),
            link_group_id: $data['link_group_id']
        );
    }

    protected function mapToArray(object $entity): array
    {
        if (!$entity instanceof LinkGroup) {
            throw new InvalidArgumentException('Invalid entity type.');
        }

        return [
            'link_group_id' => $entity->link_group_id,
            'user_id' => $entity->user_id,
            'name' => $entity->name,
            'date_created' => $entity->date_created->format('Y-m-d H:i:s'),
        ];
    }

    public function findAllUserGroups(string $userId): array
    {
        $linkGroups = array();

        $stmt = $this->db->connect()->prepare('SELECT * FROM LinkGroup WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        $results = $stmt->fetchAll();

        foreach ($results as $result) {
            $linkGroups[] = $this->mapToObject($result);
        }

        return $linkGroups;
    }

    public function findAllUserSharedGroups(string $userId): array
    {
        $linkGroupShares = array();

        $stmt = $this->db->connect()->prepare('SELECT * FROM LinkGroupShare WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        $results = $stmt->fetchAll();

        foreach ($results as $result) {
            // Get the link group details for each share
            $linkGroup = $this->findById($result['link_group_id']);
            if ($linkGroup) {
                $result['link_group'] = $linkGroup;
                $linkGroupShares[] = $this->mapToObject($result);
            }
        }

        return $linkGroupShares;
    }
    
}
