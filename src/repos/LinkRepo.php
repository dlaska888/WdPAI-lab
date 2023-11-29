<?php

require_once "src/repos/interfaces/ILinkRepo.php";
require_once "src/models/Link.php";
require_once "src/repos/Repo.php";

class LinkRepo extends Repo implements ILinkRepo
{
    public function all(): array
    {
        $links = array();

        $results = $this->db
            ->connect()
            ->query('SELECT * FROM Link')
            ->fetchAll();

        foreach ($results as $result) {
            $links[] = $this->mapToObject($result);
        }

        return $links;
    }

    public function findById(string $linkId): ?Link
    {
        $stmt = $this->db->connect()->prepare('SELECT * FROM Link WHERE link_id = :link_id');
        $stmt->execute(['link_id' => $linkId]);
        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }

        return $this->mapToObject($result);
    }

    public function insert(Link $link): Link
    {
        $sql = <<<SQL
        INSERT INTO Link (link_id, link_group_id, title, url)
        VALUES (:link_id, :link_group_id, :title, :url);
    SQL;

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([
            'link_id' => $link->link_id,
            'link_group_id' => $link->link_group_id,
            'title' => $link->title,
            'url' => $link->url,
        ]);

        return $this->findById($link->link_id);
    }

    public function update(Link $link): Link
    {
        $sql = <<<SQL
        UPDATE Link
        SET
            link_group_id = :link_group_id,
            title = :title,
            url = :url
        WHERE link_id = :link_id;
    SQL;

        $stmt = $this->db->connect()->prepare($sql);
        $stmt->execute([
            'link_id' => $link->link_id,
            'link_group_id' => $link->link_group_id,
            'title' => $link->title,
            'url' => $link->url,
        ]);

        return $this->findById($link->link_id);
    }

    public function delete(string $linkId): bool
    {
        $stmt = $this->db->connect()->prepare('DELETE FROM Link WHERE link_id = :link_id');
        return $stmt->execute(['link_id' => $linkId]);
    }

    private function mapToObject(array $linkData): Link
    {
        return new Link(
            link_group_id: $linkData['link_group_id'],
            title: $linkData['title'],
            url: $linkData['url'],
            link_id: $linkData['link_id']
        );
    }
}
