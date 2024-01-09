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
        $linkGroup = new LinkGroup(
            user_id: $data['user_id'],
            name: $data['name'],
            date_created: new DateTime($data['date_created']),
            link_group_id: $data['link_group_id']
        );
        
        return $this->joinTables($linkGroup);
    }
    
    private function mapToObjectAll(array $data) : array
    {
        return array_map(fn ($linkGroup) => $this->mapToObject($linkGroup), $data);
    }

    private function joinTables(LinkGroup $linkGroup): LinkGroup
    {
        $linkGroup->links = $this->linkRepo->findGroupLinks($linkGroup->link_group_id);
        $linkGroup->groupShares = $this->groupShareRepo->findLinkGroupShares($linkGroup->link_group_id);

        return $linkGroup;
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
        $stmt = $this->db->connect()->prepare('SELECT * FROM LinkGroup WHERE user_id = :user_id ORDER BY date_created');
        $stmt->execute(['user_id' => $userId]);
        $results = $stmt->fetchAll();

        return $this->mapToObjectAll($results);
    }

    public function findAllUserSharedGroups(string $userId): array
    {
        $stmt = $this->db->connect()->prepare(
            "SELECT LinkGroup.*
                    FROM LinkGroup
                    JOIN LinkGroupShare ON LinkGroup.link_group_id = LinkGroupShare.link_group_id
                    WHERE LinkGroupShare.user_id = :userId");
        $stmt->execute(['userId' => $userId]);
        $results = $stmt->fetchAll();

        return $this->mapToObjectAll($results);
    }


    public function findLinkGroupsByName($userId, $name) : array
    {
        $stmt = $this->db->connect()->prepare("SELECT * FROM LinkGroup WHERE user_id = :userId AND name LIKE :name");
        $stmt->execute([
            'userId' => $userId,
            'name' => '%' . $name . '%'
        ]);
        $results = $stmt->fetchAll();

        return $this->mapToObjectAll($results);
    }

    public function findSharedLinkGroupsByName($userId, $name) : array
    {
        $stmt = $this->db->connect()->prepare(
            "SELECT LinkGroup.* FROM LinkGroup 
                    JOIN LinkGroupShare ON LinkGroup.link_group_id = LinkGroupShare.link_group_id 
                    WHERE LinkGroupShare.user_id = :userId AND LinkGroup.name LIKE :name");
        $stmt->execute([
            'userId' => $userId,
            'name' => '%' . $name . '%'
        ]);
        $results = $stmt->fetchAll();

        return $this->mapToObjectAll($results);
    }


}
