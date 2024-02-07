<?php

namespace src\Repos;

use InvalidArgumentException;
use PDO;
use src\Models\Entities\Entity;
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

    protected function getEntityName(): string
    {
        return LinkGroup::class;
    }

    protected function mapToObject(array $data): LinkGroup
    {
        $linkGroup = $this->hydrator->hydrate($data, new LinkGroup());
        return $this->joinTables($linkGroup);
    }

    private function mapToObjectAll(array $data): array
    {
        return array_map(fn($linkGroup) => $this->mapToObject($linkGroup), $data);
    }

    private function joinTables(Entity $linkGroup): LinkGroup
    {
        if (!$linkGroup instanceof LinkGroup) {
            throw new InvalidArgumentException("Invalid Entity type provided for {${$this->getEntityName()}}");
        }

        $linkGroup->links = $this->linkRepo->findGroupLinks($linkGroup->id);
        $linkGroup->groupShares = $this->groupShareRepo->findLinkGroupShares($linkGroup->id);

        return $linkGroup;
    }

    public function findAllUserGroups(string $userId): array
    {
        $stmt = $this->db->connect()
            ->prepare('SELECT * FROM link_group WHERE user_id = :user_id ORDER BY date_created');
        $stmt->execute(['user_id' => $userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->mapToObjectAll($results);
    }

    public function findAllUserSharedGroups(string $userId): array
    {
        $stmt = $this->db->connect()->prepare(
            "SELECT link_group.*
                    FROM link_group
                    JOIN link_group_share ON link_group.id = link_group_share.link_group_id
                    WHERE link_group_share.user_id = :userId
                    ORDER BY date_created");
        $stmt->execute(['userId' => $userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->mapToObjectAll($results);
    }


    public function findLinkGroupsByName(string $userId, string $name): array
    {
        $stmt = $this->db->connect()->prepare(
            "SELECT * FROM link_group 
                    WHERE user_id = :userId AND 
                    LOWER(link_group.name) LIKE LOWER(:name) ORDER BY date_created");
        $stmt->execute([
            'userId' => $userId,
            'name' => '%' . $name . '%'
        ]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->mapToObjectAll($results);
    }

    public function findSharedLinkGroupsByName(string $userId, string $name): array
    {
        $stmt = $this->db->connect()->prepare(
            "SELECT link_group.* FROM link_group 
                    JOIN link_group_share ON link_group.id = link_group_share.link_group_id 
                    WHERE link_group_share.user_id = :userId AND LOWER(link_group.name) LIKE LOWER(:name)
                    ORDER BY date_created");
        $stmt->execute([
            'userId' => $userId,
            'name' => '%' . $name . '%'
        ]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->mapToObjectAll($results);
    }

}
