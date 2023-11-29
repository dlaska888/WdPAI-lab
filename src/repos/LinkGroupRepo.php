<?php

require_once "src/repos/interfaces/ILinkGroupRepo.php";
require_once "src/repos/Repo.php";
require_once "src/models/LinkGroup.php";
require_once "src/helpers/UUIDGenerator.php";

class LinkGroupRepo extends Repo implements ILinkGroupRepo
{
    public function all(): array
    {
        $linkGroups = array();

        $results = $this->db
            ->connect()
            ->query('SELECT * FROM LinkGroup')
            ->fetchAll();

        foreach ($results as $result) {
            $linkGroups[] = $this->mapToObject($result);
        }

        return $linkGroups;
    }

    public function findById(string $linkGroupId): ?LinkGroup
    {
        $stmt = $this->db->connect()->prepare('SELECT * FROM LinkGroup WHERE link_group_id = :link_group_id');
        $stmt->execute(['link_group_id' => $linkGroupId]);
        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }

        return $this->mapToObject($result);
    }

    public function insert(LinkGroup $linkGroup): LinkGroup
    {
        $sql = <<<SQL
        INSERT INTO LinkGroup (link_group_id, user_id, name, date_created)
        VALUES (:link_group_id, :user_id, :name, :date_created);
    SQL;

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([
            'link_group_id' => $linkGroup->link_group_id,
            'user_id' => $linkGroup->user_id,
            'name' => $linkGroup->name,
            'date_created' => $linkGroup->date_created->format('Y-m-d H:i:s'),
        ]);

        return $this->findById($linkGroup->link_group_id);
    }

    public function update(LinkGroup $linkGroup): LinkGroup
    {
        $sql = <<<SQL
        UPDATE LinkGroup
        SET
            user_id = :user_id,
            name = :name,
            date_created = :date_created
        WHERE link_group_id = :link_group_id;
    SQL;

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([
            'link_group_id' => $linkGroup->link_group_id,
            'user_id' => $linkGroup->user_id,
            'name' => $linkGroup->name,
            'date_created' => $linkGroup->date_created->format('Y-m-d H:i:s'),
        ]);

        return $this->findById($linkGroup->link_group_id);
    }

    public function delete(string $linkGroupId): bool
    {
        $stmt = $this->db->connect()->prepare('DELETE FROM LinkGroup WHERE link_group_id = :link_group_id');
        return $stmt->execute(['link_group_id' => $linkGroupId]);
    }

    private function mapToObject(array $linkGroupData): LinkGroup
    {
        return new LinkGroup(
            user_id: $linkGroupData['user_id'],
            name: $linkGroupData['name'],
            date_created: new DateTime($linkGroupData['date_created']),
            link_group_id: $linkGroupData['link_group_id']
        );
    }
}
