<?php

require_once "src/helpers/UUIDGenerator.php";

class LinkGroup
{
    public string $link_group_id;
    public string $user_id;
    public string $name;
    public DateTime $date_created;
    public ?array $links;
    public ?array $groupShares;

    public function __construct(
        string $user_id,
        string $name,
        DateTime $date_created = new DateTime(),
        string $link_group_id = null,
        array $links = null,
        array $groupShares = null
    ) {
        $this->link_group_id = $link_group_id ?? UUIDGenerator::genUUID();
        $this->user_id = $user_id;
        $this->name = $name;
        $this->date_created = $date_created;
        $this->links = $links;
        $this->groupShares = $groupShares;
    }

    public static function fromArray(array $data): self | null
    {
        try {
            return new self(
                $data['user_id'],
                $data['name'],
                new DateTime($data['date_created']) ?? new DateTime(),
                $data['link_group_id'] ?? null,
                $data['links'] ?? null,
                $data['groupShares'] ?? null
            );
        } catch (Throwable $e) {
            return null;
        }
    }

    public function toArray(): array
    {
        $linksArray = [];
        if (is_array($this->links)) {
            foreach ($this->links as $link) {
                $linksArray[] = $link->toArray();
            }
        }

        $groupSharesArray = [];
        if (is_array($this->groupShares)) {
            foreach ($this->groupShares as $groupShare) {
                $groupSharesArray[] = $groupShare->toArray();
            }
        }

        return [
            'link_group_id' => $this->link_group_id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'date_created' => $this->date_created->format('Y-m-d\TH:i:s'), // Format the date as needed
            'links' => $linksArray,
            'groupShares' => $groupSharesArray,
            // Add other fields as needed
        ];
    }
}
