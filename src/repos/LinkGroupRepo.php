<?php

namespace src\Repos;

use DateTime;
use InvalidArgumentException;
use src\Models\Entities\LinkGroup;

class LinkGroupRepo extends BaseRepo
{
    private LinkRepo $linkRepo;
    private LinkGroupShareRepo $groupShareRepo;

    public function __construct()
    {
        parent::__construct();
        $this->linkRepo = new LinkRepo();
        $this->groupShareRepo = new LinkGroupShareRepo();
    }

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

    public function findById(string $id): object
    {
        return $this->joinTables(parent::findById($id));
    }

    public function findAllUserGroups(string $userId): array
    {
        $linkGroups = array();

        $stmt = $this->db->connect()->prepare('SELECT * FROM LinkGroup WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
        $results = $stmt->fetchAll();

        foreach ($results as $result) {
            $linkGroup = $this->mapToObject($result);
            $linkGroup = $this->joinTables($linkGroup);
            $linkGroups[] = $linkGroup;
        }

        return $linkGroups;
    }

    public function findAllUserSharedGroups(string $userId): array
    {
        $shares = $this->groupShareRepo->findUserGroupShares($userId);
        $linkGroups = array();

        foreach ($shares as $share) {
            $linkGroups[] = $this->findById($share->link_group_id);
        }

        return $linkGroups;
    }

    private function joinTables(object $linkGroup): object
    {
        $linkGroup->links = $this->linkRepo->findGroupLinks($linkGroup->link_group_id);
        $linkGroup->groupShares = $this->groupShareRepo->findLinkGroupShares($linkGroup->link_group_id);

        return $linkGroup;
    }

}
